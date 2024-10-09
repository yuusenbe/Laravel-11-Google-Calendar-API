<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\GoogleCalendarService;
use App\Models\PublicHoliday;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class HolidayController extends BaseController
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function index(Request $request, $year)
    {
        $holidays = $this->googleCalendarService->getHolidays($year);

        //echo "<pre>";
        //print_r($holidays);
        //exit();

        foreach ($holidays as $holiday) {
            if ($holiday && isset($holiday->start->date) && isset($holiday->summary)) {
                $holidayData = [
                    'holiday_date' => $holiday->start->date,
                    'holiday_name' => $holiday->summary,
                    'holiday_end_date' => isset($holiday->end->date) ? $holiday->end->date : null,
                    'regions' => $this->retrieveRegions($holiday->description)
                ];

                $this->insertHoliday($holidayData);
            } else {
                Log::warning('Invalid holiday data: ' . json_encode($holiday));
            }
        }

        return response()->json($holidays);
    }

    protected function retrieveRegions($description)
    {
        return array_map('trim', explode(',', $description));
    }

    protected function insertHoliday($holidayData)
    {
        PublicHoliday::updateOrCreate(
            ['holiday_date' => $holidayData['holiday_date']],
            [
                'holiday_name' => $holidayData['holiday_name'],
                'holiday_end_date' => $holidayData['holiday_end_date'],
                'regions' => implode(',', $holidayData['regions'])
            ]
        );
    }

    public function getHolidaysByYearAndRegions($year, $regions = null)
    {
        try {
            
            $holidays = $this->googleCalendarService->getHolidays($year);

            //echo "<pre>";
            //print_r($holidays);
            //exit();

            $regionArray = $regions ? explode(',', $regions) : [];

            $filteredHolidays = [];

            foreach ($holidays as $holiday) {
                if ($holiday && isset($holiday->start->date) && isset($holiday->summary)) {
                    
                    $holidayRegions = $this->extractRegions($holiday->description);

                    
                    $holidayData = $this->formatHoliday($holiday, $holidayRegions);

                    
                    if (strtolower($holidayData['regions']) === 'public holiday') {
                    
                        $holidayData['regions'] = 'Public Holiday';
                    } else {
                        
                        $filteredRegions = array_intersect($regionArray, $holidayRegions);

                        
                        if (!empty($filteredRegions)) {
                            $holidayData['regions'] = implode(', ', $filteredRegions);
                        } else {
                            
                            continue;
                        }
                    }

                    
                    $this->storeHoliday($holidayData);

                    
                    $filteredHolidays[] = $holidayData;
                } else {
                    Log::warning('Invalid holiday data: ' . json_encode($holiday));
                }
            }

            
            return response()->json($filteredHolidays);
        } catch (Exception $e) {
            Log::error('Error fetching holidays: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch holidays'], 500);
        }
    }

    protected function formatHoliday($holiday, $regions)
    {
        $isPublicHoliday = strtolower($holiday->summary) === 'public holiday';

        return [
            'holiday_date' => $holiday->start->date,
            'holiday_name' => $holiday->summary,
            'holiday_end_date' => isset($holiday->end->date) ? $holiday->end->date : null,
            'regions' => $isPublicHoliday ? 'Public Holiday' : implode(', ', $regions)
        ];
    }

    protected function extractRegions($description)
    {
        
        $description = str_replace('Public holiday in ', '', $description);
        return array_map('trim', explode(',', $description));
    }

    protected function storeHoliday($holidayData)
    {
        try {
            Log::info('Inserting holiday data: ' . json_encode($holidayData));

            PublicHoliday::updateOrCreate(
                ['holiday_date' => $holidayData['holiday_date']],
                [
                    'holiday_name' => $holidayData['holiday_name'],
                    'holiday_end_date' => $holidayData['holiday_end_date'],
                    'regions' => $holidayData['regions']
                ]
            );

            Log::info('Holiday inserted/updated successfully for date: ' . $holidayData['holiday_date']);
        } catch (Exception $e) {
            Log::error('Error inserting holiday: ' . $e->getMessage());
        }
    }
}
