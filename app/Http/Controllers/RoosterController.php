<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoosterController extends Controller
{
    public function index()
    {
        return view('rooster.index');
    }

    public function upload(\App\Http\Requests\RoosterUploadRequest $request)
    {
        $roosterFile = $request->file('rooster-file')->store('roosters');

        $skipRows = 0;
        /** @var \Maatwebsite\Excel\Readers\LaravelExcelReader $excelFile */
        $excelFile = \Excel::selectSheetsByIndex(0)->load(\Storage::path($roosterFile));
        $excelFile->each(function ($rowData) use(&$skipRows) {
            /** @var \Maatwebsite\Excel\Collections\CellCollection $rowData */
            if($rowData->get('tot.uren_jcontract_jverschil_j') == null) {
                $skipRows++;
            }
        });

        $excelFile->skipRows($skipRows);
        $excelData = $excelFile->toArray();

        $roosters = [];

        $currentEmployee = 0;
        /** @var \App\Objects\Rooster $rooster */
        $rooster = null;
        foreach ($excelData as $index => $excelRow)
        {
            $rowValues = array_values($excelRow);

            if ($currentEmployee == 0) {
                $rooster = new \App\Objects\Rooster();
                $rooster->naam = $rowValues[0];
                $rooster->functie = $rowValues[1];

                $roosterValues = array_slice($excelRow, 5);
                foreach ($roosterValues as $dateString => $workType) {
                    $date = \App\Helper::parseDate($dateString);
                    $rooster->roosterPerDag[] = [
                        'datum' => $date,
                        'werkType' => $workType
                    ];
                }
            } else if ($currentEmployee == 1) {
                $rooster->voornaam = $rowValues[0];
            } else if ($currentEmployee == 2) {
                $rooster->personeelsNummer = $rowValues[0];
            }

            $currentEmployee++;

            if ($currentEmployee == 3) {
                $roosters[] = clone $rooster;
                $rooster = null;
                $currentEmployee = 0;
            }
        }

        return view('rooster.select', ['file' => $roosterFile, 'roosters' => $roosters]);
    }

    public function generate(\App\Http\Requests\GenereerKalenderRequest $request)
    {
        $roosterIndex = $request->post('rooster-index');

        /** @var \App\Objects\Rooster[] $roosterData */
        $roosterData = unserialize($request->post('rooster-data'));
        $selectedRooster = $roosterData[$roosterIndex];

        $calendar = new \Eluceo\iCal\Component\Calendar('zsm-rooster.unxsist.nl');
        foreach ($selectedRooster->roosterPerDag as $dagRooster) {
            /** @var \Carbon\Carbon $dagRoosterDatum */
            $dagRoosterDatum = clone $dagRooster['datum'];
            $dagRoosterDatum->setTimezone(new \DateTimeZone('Europe/Amsterdam'));
            /** @var \Carbon\Carbon $dagRoosterDatumEinde */
            $dagRoosterDatumEinde = clone $dagRooster['datum'];
            $dagRoosterDatumEinde->setTimezone(new \DateTimeZone('Europe/Amsterdam'));

            if ($dagRooster['werkType'] != null) {
                $event = new \Eluceo\iCal\Component\Event();
                $event->setGeoLocation(new \Eluceo\iCal\Property\Event\Geo(52.3715352, 7.0100449));
                $event->setLocation('Berghummerstraat 15, 7591 GX, Denekamp', 'Gerardus Majella');

                $fullDay = false;
                switch ($dagRooster['werkType']) {
                    case 'S00':
                        $dagRoosterDatum->setTime(8, 0);
                        $dagRoosterDatumEinde->setTime(17, 0);
                        break;
                    case 'VK8':
                        $fullDay = true;
                        break;
                    case 'D14':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(15, 30);
                        break;
                    case 'D05':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(14, 30);
                        break;
                    case 'D04':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(15, 30);
                        break;
                    case 'D08':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(13, 30);
                        break;
                    case 'O05':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(10, 0);
                        break;
                    case 'D24':
                        $dagRoosterDatum->setTime(8, 0);
                        $dagRoosterDatumEinde->setTime(16, 30);
                        break;
                    case 'A12':
                        $dagRoosterDatum->setTime(15, 15);
                        $dagRoosterDatumEinde->setTime(22, 45);
                        break;
                    case 'A21':
                        $dagRoosterDatum->setTime(16, 0);
                        $dagRoosterDatumEinde->setTime(19, 30);
                        break;
                    case 'A34':
                        $dagRoosterDatum->setTime(16, 0);
                        $dagRoosterDatumEinde->setTime(21, 30);
                        break;
                    case 'N06':
                        $dagRoosterDatum->setTime(22, 30);
                        $dagRoosterDatumEinde->addDays(1)->setTime(7, 0);
                        break;
                    case 'O04':
                        $dagRoosterDatum->setTime(7, 0);
                        $dagRoosterDatumEinde->setTime(11, 0);
                        break;
                    default:
                        $fullDay = true;
                        break;
                }

                if ($fullDay) {
                    $event->setUseTimezone(true)->setDtStart($dagRoosterDatum)->setDtEnd($dagRoosterDatum)->setNoTime(true);
                } else {
                    $event->setUseTimezone(true)->setDtStart($dagRoosterDatum)->setDtEnd($dagRoosterDatumEinde);
                }

                $event->setSummary($dagRooster['werkType'].' - '.$selectedRooster->naam);
                $calendar->addComponent($event);
            }
        }

        return response($calendar->render())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="rooster.ics"');
    }
}
