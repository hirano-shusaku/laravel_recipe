<x-app-layout>
  <div class="grid grid-cols-4 mb-6">
    <div class="col-span-1 bg-white rounded p-4">
      <h3 class="text-2xl font-bold mb-2">レシピ検索</h3>
        <ul class="ml-6 mb-4">
          <li><a href="">すべてのレシピ</a></li>
          <li><a href="">人気のレシピ</a></li>
        </ul>
        <h3 class="text-2xl font-bold mb-2">レシピ投稿</h3>
        <ul class="ml-6 mb-4">
          <li><a href="">すべてのレシピ</a></li>
          <li><a href="">人気のレシピ</a></li>
        </ul>
    </div>
    <div class="col-span-2 bg-white rounded p-4">
      <h2 class="text-2xl font-bold mb-2">新着レシピ</h2>
      @foreach($recipes as $recipe)
        @include('recipe.partial.h-card')
      @endforeach
      <a href="{{ route('recipe.index') }}" class="text-gray-600 block text-right">すべてのレシピへ→</a>
      
    </div>
    <div class="col-span-1 bg-gray ml-4">
      <img src="/images/ad.png" alt="広告">
    </div>
  </div>
    
  <div class="grid grid-cols-4">
    <div class="col-span-3 bg-white rounded p-4">
      <h2 class="text-2xl font-bold mb-2">人気のレシピ</h2>
      <div class="flex justify-between items-center mb-6">
        @foreach ($popular as $p)
        <div class="rounded overflow-hidden shadow-lg">
          <img class="max-h-44 w-full object-cover" src="{{ $p->image }}" alt="{{ $p->title }}">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2">{{ $p->title }}</div>
            <p class="text-gray-700 text-base">
              {{ $p->description }}
            </p>
          </div>
        </div>
        @endforeach
      </div>
      <a href="" class="text-gray-600 block text-right">人気のレシピへ→</a>
    </div>
    <div class="col-span-1"></div>
  </div>
</x-app-layout>