<x-app-layout>
  <div class="w-10/12 p-4 mx-auto bg-white rounded">
    {{ Breadcrumbs::render('show', $recipe) }}
    <!--レシピの詳細-->
    <div class="grid grid-cols-2 rounded border border-gray-500 mt-4">
      <div class="col-span-1">
        <img class="object-cover w-full aspect-square"
      src="{{ $recipe->image }}" alt="{{ $recipe->title }}">
      </div>
      <div class="col-span-1 p-4">
        <p class="mb-4">{{ $recipe->description }}</p>
        <p class="mb-4 text-gray-500">{{ $recipe->user->name }}</p>
        <h4 class="text-2xl font-bold mb-2">材料</h4>
        <ul class="text-gray-500 ml-6">
      @foreach ( $recipe->ingredients as $i )
          <li>{{ $i->name }}：{{ $i->quantity }}</li>
      @endforeach
        </ul>
      </div>
    </div>
    <br>
    {{-- ステップすの詳細 --}}
    <div>
      <h4 class="text-2xl font-bold mb-6">作り方</h4>
      <div class="grid grid-cols-4 gap-4">
    @foreach ($recipe->steps as $s )
      <div class="mb-2 background-color p-2">
        <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full mr-4 mb-4">
          {{ $s->step_number }}
        </div>
        <p>{{ $s->description }}</p>
      </div>
    @endforeach
      </div>
    </div>
  </div>
  @if ($is_my_recipe)
    <a href="{{ route('recipe.edit',$recipe) }}" class="block w-2/12 p-4 my-4 mx-auto bg-white rounded text-center text-green-500 border border-green-500 hover:text-white">
      編集する
    </a>
  @endif
  {{-- レビューの詳細 --}}
  @guest()
    <p class="text-center text-gray-500">レビューを投稿するには
      <a href="{{ route('login') }}" class="text-blue-700">ログイン</a>
      してください
    </p>
  @endguest
  @auth
  @if ($is_my_review)
    <p class="text-center text-gray-500 mb-4">
      レビューは投稿済みです
    </p>
  @elseif ($is_my_recipe)
    <p class="text-center text-gray-500 mb-4">
      自分のレシピには投稿できません
    </p>
  @else
  <div class="w-10/12 p-4 mx-auto bg-white rounded mb-6">
    <form action="{{ route('review.store', $recipe) }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="rating" >
          評価
        </label>
        <select name="rating" id="rating" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3" selected>3</option>
          <option value="4">4</option>
          <option value="5">5</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">
          コメント          
        </label>
        <textarea name="comment" id="comment" cols="30" rows="10" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 pr-8 rounded"></textarea>
      </div>
      <div class="flex items-center justify-between">
        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">
          レビューを投稿する
        </button>
      </div>
    </form>
  </div>
  @endif
  @endauth

  <div class="w-10/12 p-4 mx-auto bg-white rounded">
    <h4 class="text-2xl font-bold mb-2">レビュー</h4>
  @foreach ( $recipe->reviews as $r )
    <div class="background-color rounded mb-4 p-4">
      <div class="flex mb-4">
    @for ($i = 0; $i < $r->rating; $i++)
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-600">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
      </svg>
    @endfor
        <p class="ml-2">{{ $r->comment }}</p>
      </div>
      <p class="text-gray-600 font-bold">{{ $r->user->name }}</p>
    </div>
  @endforeach
  @if (count($recipe->reviews) === 0 )
    <p>レビューはまだありません</p>
  @endif
  </div>
</x-app-layout>
