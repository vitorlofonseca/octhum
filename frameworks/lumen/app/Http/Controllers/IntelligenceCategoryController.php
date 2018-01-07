<?php

namespace App\Http\Controllers;

use App\IntelligenceCategory;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

class IntelligenceCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function createIntelligenceCategory(Request $request){

        try{

            if (!$request->input('category'))
                throw new Exception('A category is necessary');

            $objIntelligenceCategory = IntelligenceCategory::create($request->all());

            return response()->json($objIntelligenceCategory);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }

    }

    public function getIntelligenceCategory($id){

        try {

            $objIntelligenceCategory = IntelligenceCategory::find($id);

            if (!is_object($objIntelligenceCategory))
                throw new Exception('Category\'s id invalid or doesn\'t exist');

            return response()->json($objIntelligenceCategory);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }

    public function deleteIntelligenceCategory($id){

        try {

            $objIntelligenceCategory = IntelligenceCategory::find($id);

            if (!is_object($objIntelligenceCategory))
                throw new Exception('Category\'s id invalid or doesn\'t exist');

            $objIntelligenceCategory->delete();

            return response()->json('Intelligence Category deleted');

        } catch(Exception $e){

            return response()->json($e->getMessage());

        }

    }

    public function updateIntelligenceCategory(Request $request,$id){

        try {

            $objIntelligenceCategory = IntelligenceCategory::find($id);

            if (!is_object($objIntelligenceCategory))
                throw new Exception('Category\'s id invalid or doesn\'t exist');

            if($request->input('category')) {
                $objIntelligenceCategory->category = $request->input('category');
                $objIntelligenceCategory->save();
            }

            return response()->json('Intelligence Category uploaded');

        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }


    public function getAllIntelligenceCategories(){

        $aObjIntelligenceCategory = IntelligenceCategory::all();

        return response()->json($aObjIntelligenceCategory);

    }

}
