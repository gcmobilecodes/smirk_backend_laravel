<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Str;

/**
 * Models
 * */

use App\Models\Card;
use App\Models\Category;
use App\Models\Show;
use DataTables;
use App\Traits\OutputTrait;

class CardsController extends Controller
{
    use OutputTrait;

    /**
     * cardsList
     *
     * @param  Request $request
     * @param  Card $card
     * @return void
     */
    public function cardsList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $datas = Card::join('catgories','cards.category_id','=','catgories.id')
                ->where('cards.status',1)
                ->orderBy('cards.id','desc')
                ->select('cards.*','catgories.name as cat_name')->get();
                return Datatables::of($datas)
              ->addColumn('image', function ($report) { 
                        $url=asset("storage/".$report->card_image); 
                        return '<img src='.$url.' border="0" width="120" class="img-rounded" align="center" />'; 
                })
            ->addColumn('action', function ($report) {
                return '<a href="/cards/edit/'.$report->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <a href="cards/delete/"'.$report->id .'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</a>   ';
            })
                   ->rawColumns(['image', 'action'])
                 ->make(true);
            }     
            return view('cards.list');
        } catch (Exception $exception) {
            return view('exceptions', compact('exception'));
        }
    }












    

    /**
     * deletePreference
     *
     * @param  Request $request
     * @param  Card $card
     * @return void
     */
    public function deleteCard($id)
    {
        $user = Card::where('id',$id)->update(['status'=>0]); 
        return redirect('/cards/list')->with('success', 'Category Deleted Successfully');;

    }

    /**
     * addCard
     *
     * @param  Request $request
     * @param  Card $card
     * @return void
     */
    public function addCard(Request $request, Card $card)
    {
        try {

            $card = new Card();
            $card->name = $request->name;
            $card->category_id = $request->category_id;

            if ($request->card_image) {
                $file =  $this->upload_file($request->card_image, 'card_image');
                $card->card_image =$file;
            }
            $card->save();
            if ($card) {
                return redirect('/cards/list')->with('success', 'Card added Successfully');;
            } else {
                return redirect()->back()->with('error', 'Not added');
            }
        } catch (Exception $exception) {
            return view('exceptions', compact('exception'));
        }
    }
    public function editCard($id){
        $data = Card::join('catgories','cards.category_id','=','catgories.id')
        ->where('cards.status',1)
        ->where('cards.id',$id)
        ->select('cards.*','catgories.name as cat_name')
        ->first();
        return view('cards.edit',compact('data'));
    }
    public function updateCard(Request $request,$id){
        $cat = Category::where('id',$request->category_id)->first();
      $data = Card::where('id',$id)->first();
      if ($request->card_image) {
        $file =  $this->upload_file($request->card_image, 'card_image');
        $data->card_image =$file; 
    }else{
      $data->card_image =$data->card_image; 
    }
      $data->name = $request->name;
      $data->show_id = $cat->show_id;
      $data->save();
       return redirect('/cards/list')->with('success', 'Card Updated Successfully');;
    }
}
