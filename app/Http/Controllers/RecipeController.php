<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Foreach_;
use App\Models\Step;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RecipeCreateRequest;
use App\Http\Requests\RecipeUpdateRequest;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function home()
    {
        $recipes = Recipe::select('recipes.id','recipes.title','recipes.description','recipes.created_at','recipes.image','users.name')
            ->join('users','users.id','=','recipes.user_id')
            ->orderby('recipes.created_at','desc')
            ->limit(3)
            ->get();
        //dd($recipes);

        $popular = Recipe::select('recipes.id','recipes.title','recipes.description','recipes.created_at','recipes.image','recipes.views','users.name')
            ->join('users','users.id','=','recipes.user_id')
            ->orderby('recipes.views','desc')
            ->limit(2)
            ->get();
            //dd($popular);
        return view('home',compact('recipes','popular'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        //dd($filters);

        $query = Recipe::select('recipes.id','recipes.title','recipes.description','recipes.created_at','recipes.image','users.name'
        ,DB::raw('AVG(reviews.rating) as rating'))
            ->join('users','users.id','=','recipes.user_id')
            ->leftJoin('reviews','reviews.recipe_id','=','recipes.id')
            ->groupBy('recipes.id')
            ->orderby('recipes.created_at','desc');

        if(!empty($filters)){//もし$filterが空でなかったら
            if(!empty($filters['categories'])){//もし$filtersのcategoriesが空でなかったら
                $query->whereIn('recipes.category_id',$filters['categories']);
                //$queryからrecipes.category_idと$filters->categoriesが合致して含まれるもの全部
            }
            if(!empty($filters['rating'])){
                $query->havingRaw('AVG(reviews.rating) >= ?',[$filters['rating']])
                ->orderBy('rating','desc');
            }
            if(!empty($filters['title'])){
                $query->where('recipes.title','like','%'.$filters['title'].'%');
            }
        }
        $recipes = $query->paginate(5);
         //dd($recipes);

        $categories = Category::all();

        return view('recipe.index',compact('recipes','categories','filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('recipe.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecipeCreateRequest $request)
    {
        $posts = $request->all();
        $uuid = Str::uuid()->toString();
        $user_id = auth()->user()->id;
        // dd($posts,$user_id);
        
        $image = $request->file('image');
        $path = Storage::disk('s3')->putFile('recipe',$image,'public');
        // dd($path);
        $url = Storage::disk('s3')->url($path);
        // dd($url);
        //---画像のUPの流れ---
        //s3に画像をUP
        //s3のURLを取得
        //DBにはURLを保存
        try{
            DB::beginTransaction();
            Recipe::insert([
                'id' => $uuid,
                'title' => $posts['title'],
                'description' => $posts['description'],
                'category_id' => $posts['category'],
                'image' => $url,
                'user_id' => $user_id,
            ]);

            $ingredients = [];
            foreach( $posts['ingredients'] as $key=> $ingredient)
            {
                $ingredients[$key] = [
                    'recipe_id' => $uuid,
                    'name' => $ingredient['name'],
                    'quantity' => $ingredient['quantity']
                ];
            }
            // dd($ingredients);
            Ingredient::insert($ingredients);

            $steps = [];
            foreach($posts['steps'] as $key =>$step )
            {
                $steps[$key] = [
                    'recipe_id' => $uuid,
                    'step_number' => $key +1,
                    'description' => $step
                ];
            }
            STEP::insert($steps);
            // dd($steps);  
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            Log::debug(print_r($th->getMessage(),true));
            throw $th;
        }

        flash()->success('レシピを投稿しました!!!');
        return redirect()->route('recipe.show',$uuid);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe = Recipe::with(['ingredients','steps','reviews','user'])
            ->where('recipes.id',$recipe->id)
            ->first();

        $user_id = auth()->user()->id;
        $id = $recipe->id;
        $recipe_recode = Recipe::find($id);
        $recipe_recode->increment('views');
        //dd($recipe);
        //レシピの投稿者とログインユーザが同じかどうか
        $is_my_recipe = false;
        if(Auth::check() && ($user_id === $recipe['user_id']))
        {
            $is_my_recipe = true;
        }
        
        $is_my_review = false;
        if( Auth::check() ){
            $is_my_review = $recipe->reviews->contains('user_id', Auth::id());
        }

        
        //リレーションで材料とステップ（工程）を取得

        return view('recipe.show',compact('recipe','is_my_recipe','is_my_review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $recipe = Recipe::with(['ingredients','steps','reviews','user'])
            ->where('recipes.id',$recipe->id)
            ->first();

        if( !Auth::check() || auth()->user()->id !== $recipe['user_id'])
        {
            abort(403);
        }

        $id = $recipe->id;
        $recipe_recode = Recipe::find($id);
        $recipe_recode->increment('views');

        $categories = Category::all();

        return view('recipe.edit',compact('recipe','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeUpdateRequest $request, string $id)
    {
        $posts = $request->all();
        // dd($posts);
        //画像の分岐
        $update_array = [
            'title' => $posts['title'],
            'description' => $posts['description'],
            'category_id' => $posts['category_id'],
        ];

        if($request->hasFile('image')){
            $image = $request->file('image');
        //$requestからimageファイルを取得
            $path = Storage::disk('s3')->putFile('recipe',$image,'public');
        //s3に画像をUPロード
            $url = Storage::disk('s3')->url($path);
        //DBにURLを取得
            $update_array['image'] = $url;
        }
        
        
        
        try{
           DB::beginTransaction();
            Recipe::where('id',$id)->update( $update_array);
            Ingredient::where('recipe_id', $id)->delete();
            Step::where('recipe_id', $id)->delete();

            $ingredients = [];
                foreach( $posts['ingredients'] as $key=> $ingredient)
                {
                    $ingredients[$key] = [
                        'recipe_id' => $id,
                        'name' => $ingredient['name'],
                        'quantity' => $ingredient['quantity']
                    ];
                }
                // dd($ingredients);
            Ingredient::insert($ingredients);

            $steps = [];
                foreach($posts['steps'] as $key =>$step )
                {
                    $steps[$key] = [
                        'recipe_id' => $id,
                        'step_number' => $key +1,
                        'description' => $step
                    ];
                }
            STEP::insert($steps);
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            Log::debug(print_r($th->getMessage(),true));
            throw $th;
        }
        flash()->success('レシピを更新しました！');

        return redirect()->route('recipe.show',$id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // dd($id);
        Recipe::where('id',$id)->delete();
        flash()->warning('レシピを削除しました！');

        return redirect()->route('home');

    }
}
