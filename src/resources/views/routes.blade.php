Route::name('{{ $routeGroupName }}.')->group(function () {
    Route::get('/{{ $routeGroupName }}', [{{ $controllerClass }}, 'overview'])->name('overview');
    Route::get('/{{ $routeGroupName }}/{id}', [{{ $controllerClass }}, 'detail'])->name('detail');
});