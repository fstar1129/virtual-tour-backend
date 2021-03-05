<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\TourResource;
use App\Http\Resources\TourExportResource;
use App\Tour;
use App\User;
use App\Http\Requests\CreateTourRequest;
use App\Http\Controllers\TourController as BaseTourController;
use App\Http\Requests\Admin\TransferTourRequest;
use Config;

class TourController extends BaseTourController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        return TourResource::collection(
            Tour::with('creator')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateTourRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTourRequest $request)
    {
        $client = \App\Client::findOrFail($request->user_id);

        if ($client->tours()->count() >= $client->tour_limit) {
            return $this->fail(422, 'Tour limit reached.');
        }

        if ($tour = Tour::create($request->validated())) {
            return $this->success("The tour {$tour->name} was created successfully.", new TourResource(
                $tour->fresh()->load(['stops', 'route'])
            ));
        }

        return $this->fail();
    }

    /**
     * Transfer tour ownership to the requested user.
     *
     * @param TransferTourRequest $request
     * @param Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function transfer(TransferTourRequest $request, Tour $tour)
    {
        $newClient = \App\Client::findOrFail($request->user_id);

        if ($newClient->tours_left == 0) {
            return $this->fail(422, 'Operation failed.  This would exceed the number of tours for the selected client.');
        }

        $tour->update(['user_id' => $request->user_id]);

        return $this->success('Tour was successfully transfered.');
    }

    /**
     * Transfer tour ownership to the requested user.
     *
     * @return \Illuminate\Http\Response
     */
    public function export() {
        $published_tours = Tour::whereNotNull('published_at')->where('user_id', 2)->get();
        $tour_data = TourExportResource::collection($published_tours);
        $client = new \Google_Client();
        $client->setApplicationName("Junket List");
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path("credentials.json"));
        $client->setAccessToken(config('services.google-spreadsheet.key_id_of_service_account'));
        
        $service = new \Google_Service_Sheets($client);
        $sheet_id = config('services.google-spreadsheet.sheet_id');
        $options = array('valueInputOption' => 'RAW');
        $values = [
            ["Tour ID", "Tour Title", "Location", "Tour Type", "Number of stops", "Total Audio(mins)", "Walking distance(miles)", "Date of creation", "Tour Description", "Prize", "Price(USD)", "FareHarbor Webhook"]
        ];
        foreach($tour_data as $tour) {
            $myArray = json_decode(json_encode($tour), true); 
            $array = array_values($myArray);
            array_push($values, $array);
        }
        // return $values;
        $body   = new \Google_Service_Sheets_ValueRange(['values' => $values]);
        $result = $service->spreadsheets_values->update($sheet_id, 'A1:M500', $body, $options);
        return [$tour_data];
    }
}
