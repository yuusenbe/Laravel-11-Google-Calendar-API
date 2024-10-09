<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();

        $this->client->setDeveloperKey(env('GOOGLE_API_KEY'));
    }

    public function getHolidays($year)
    {
        try {
            
            $service = new Calendar($this->client);

            $calendarId = 'en.malaysia#holiday@group.v.calendar.google.com';

            $start = date('Y-m-d\TH:i:s\Z', strtotime($year . '-01-01T00:00:00'));
            $end = date('Y-m-d\TH:i:s\Z', strtotime($year . '-12-31T23:59:59'));

            $optParams = [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ];

            $events = $service->events->listEvents($calendarId, $optParams);

            return $events->getItems();
            
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
