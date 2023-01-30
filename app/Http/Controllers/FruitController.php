<?php

namespace App\Http\Controllers;

use App\Models\Fruit;
use Illuminate\Support\Facades\Https;
use Illuminate\Http\Request;

class FruitController extends Controller
{
    /**
     * Returns the fruit page
     * passes $fruits from Fruits Table
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fruitIndex(Request $request) {
        $fruits = Fruit::orderBy('name')->get();

        return view('fruit')->with(['fruits' => $fruits]);
    }

    /**
     * Create Fruit adds new Fruit
     * Takes In Name Of Fruit
     * @param Request $request
     * @return void
     */
    public function createFruit(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:fruits',
        ]);

        if($validated) {
            $fruit = new Fruit;
            $fruit->name = $request->input('name');
            $fruit->save();
        }
    }

    /**
     * Remove Fruit takes in
     * ID from Blade to remove.
     * @param Request $request
     * @return void
     */
    public function removeFruit(Request $request) {
        $validated = $request->validate([
            'id' => 'exists:fruits',
        ]);

        if($validated) {
            $fruit = Fruit::find($request->input('id'));
            $fruit->delete();
        }
    }

    /** Grabs contents from Json Endpoint
     *  For fruit data
     * @param Request $request
     * @return array
     */
    // This doesn't work. I've tried to gain access to that json file all weekend and it refuses connection or times out.
    public function getJsonFruit(Request $request) {
        try {
            $response = file_get_contents('https://dev.shepherd.appoly.io/fruit.json');
        }catch(Exception $error) {
            dd($error);
        }
        if(empty($response)) {
            $jsonArray[0]['name'] = 'Test';
        } else {
            $json = json_decode($response);
            $jsonArray = array($json);
        }

        /*
         * save the result in the model to be loaded into the page.
         */
        foreach($jsonArray as $fruitJson) {
            $fruit = new Fruit;
            $fruit->name = $fruitJson['name'] ? $fruitJson['name'] : '';
//            $fruit->shape = $fruitJson['shape'];
//            $fruit->size = $fruitJson['size'];
//            $fruit->price = $fruitJson['price'];
            if($fruitJson['name'] == '') {
                continue;
            } else {
                $fruit->save();
            }
        }
    }
}
