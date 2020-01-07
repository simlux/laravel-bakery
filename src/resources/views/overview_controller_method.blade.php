    /**
     * {{ '@' }}param Request $request
     *
     * {{ '@' }}return \Illuminate\Contracts\View\Factory|View
     */
    public function overview(Request $request): View
    {
        $results = {{ $model->getName() }}::all();

        return view('{{ $table }}.overview')
            ->with('results', $results);
    }