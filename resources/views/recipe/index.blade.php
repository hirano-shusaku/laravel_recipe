<x-app-layout>
  <div class="grid grid-cols-3 gap-4">
    <div class="col-span-2 bg-white rounded p-4">
      
      {{ Breadcrumbs::render('index') }}
      <div class="mb-4"></div>

      @foreach($recipes as $recipe)
        @include('recipe.partial.h-card')
      @endforeach

      {{ $recipes->links() }}
    </div>
    <div class="col-span-1 bg-white p-4 h-max sticky top-4">
      <form action="{{route('recipe.index')}}" method="GET">
        <div class="flex">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <h3 class="text-xl font-bold mb-4 text-gray-800">レシピ検索</h3>
        </div> 

          <div class="mb-4 p-6 border border-gray-300">
            <label class="text-lg text-gray-800">評価</label>
            <div class="ml-4 mb-2">
              <input type="radio" name="rating" value="0" id="rating0" 
                {{ ($filters['rating'] ?? null)== null ? 'checked' : ''}} />
              <label for="rating0">指定なし</label>
            </div>
            <div class="ml-4 mb-2">
              <input type="radio" name="rating" value="3" id="rating3"
                {{ ($filters['rating'] ?? null)== "3" ? 'checked' : ''}} />
              <label for="rating3">3以上</label>
            </div>
            <div class="ml-4 mb-2">
              <input type="radio" name="rating" value="4" id="rating4"
              {{ ($filters['rating'] ?? null) == "4" ? 'checked' : ''}} />
              <label for="rating4">4以上</label>
            </div>
          </div>

          <div class="mb-4 p-6 border border-gray-300">
            <label class="text-lg text-gray-800">カテゴリー</label>
            @foreach ( $categories as $cate )
              <div class="ml-4 mb-2">
                <input type="checkbox" name="categories[]" value="{{$cate->id}}" id="cate{{$cate->id}}" 
                {{in_array($cate['id'],$filters['categories'] ?? []) ? 'checked' : '' }} />
                <label for="cate{{$cate->id}}"">{{$cate->name}}</label>
              </div>
            @endforeach
          </div>

          <input type="text" name="title" value="{{ $filters['title'] ?? ''}}" placeholder="レシピ名を入力" class="border border-gray-300 p-2 mb-4 w-full">
          <div class="text-center">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4">検索</button>
          </div>
          
          
      </form>
    </div>
  </div>
</x-app-layout>

