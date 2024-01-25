<x-app-layout>
  <x-slot name="script">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.13.0/Sortable.min.js"></script>
    <script src="/js/recipe/create.js"></script>
  </x-slot>
  
  <form action="{{ route('recipe.update',$recipe) }}" method="POST" class="w-10/12 p-4 mx-auto bg-white rounded" enctype="multipart/form-data">
    @csrf
    @method('patch')

    {{ Breadcrumbs::render('edit') }}
    <div class="grid grid-cols-2 rounded border border-gray-500 my-4">
      <div class="col-span-1">
        <img id="preview" class="object-cover w-full aspect-video"
      src="{{ $recipe['image'] }}" alt="recipe-image">
        <input type="file" id="image" name="image" class="border border-gray-300 p-2 mb-4 w-full rounded">
      </div>
      <div class="col-span-1 p-4">
        <input type="text" name="title" value="{{ $recipe['title'] }}" placeholder="レシピ名" class="border border-gray-600 p-2 mb-4 w-full rounded">
        <textarea name="description" placeholder="レシピの説明" class="border border-gray-300 p-2 mb-4 w-full rounded">{{ $recipe['description'] }}</textarea>
        <select name="category_id" class="border-gray-300 p-2 mb-4 w-full rounded">
          <option value="">カテゴリー</option>
       
      @foreach($categories as $c)
      <option value="{{ $c['id'] }}" {{ ($recipe['category_id'] ?? null) == $c['id'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
      @endforeach
        </select>

        <h4 class="text-bold text-xl mb-2">材料を入力</h4>
        <div id="ingredients">
    
  
    @foreach ( $recipe['ingredients'] as $i => $oi )
      <div class="ingredient flex items-center mb-4">
        <x-bar3></x-bar3>
        <input type="text" value="{{ $oi['name'] }}" name="ingredients[{{ $i }}][name]" placeholder="材料名" class="ingredient-name border border-gray-300 p-2 ml-4 w-full rounded">
        <p class="mx-2">:</p>
        <input type="text" value="{{ $oi['quantity'] }}" name="ingredients[{{ $i }}][quantity]" placeholder="分量" class="ingredient-quantity border border-gray-300 p-2 w-full rounded">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 ingredient-delete text-gray-600 ml-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
        </svg>  
      </div> 
    @endforeach
        </div>
        <button type="button" id="ingredient-add" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">材料を追加する</button>
      </div>
    </div>
    <div class="flex justify-center">
      <x-primary-button class="bg-green-700 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">レシピを更新する</x-primary-button>
    </div>
    <hr class="my-4">
    <h4 class="text-bold text-xl mb-4">手順を入力</h4>
    <div id="steps">

    @foreach ( $recipe['steps'] as $i => $os)
      <div class="step flex justify-between items-center mb-2">
        <x-bar3></x-bar3>      
        <p class="step-number w-16">手順{{ $os['step_number'] }}</p>
        <input type="text" value="{{ $os['description'] }}" name="steps[]" placeholder="手順を入力" class="border border-gray-300 p-2 w-full rounded">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 step-delete text-gray-600 ml-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
        </svg>     
      </div>
    @endforeach   
    </div>
    <button type="button" id="step-add" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">手順を追加する</button>
  </form>
  <form action="{{ route('recipe.delete',$recipe) }}" method="POST" class="w-10/12 mx-auto my-6">
    @csrf
    @method('DELETE')
    <button id="delete" type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
        レシピを削除する
    </button>
  </form>
</x-app-layout>