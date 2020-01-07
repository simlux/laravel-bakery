    /**
     * {{ '@' }}param Request $request
     * {{ '@' }}param int     $id
     *
     * {{ '@' }}return \Illuminate\Contracts\View\Factory|View
     */
    public function detail(Request $request, int $id): View
    {
        return view('{{ $table }}.detail')
            ->with('result', {{ $model->getName() }}::find($id));
    }