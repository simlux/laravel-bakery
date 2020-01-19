    /**
     * {{ '@' }}return string
     */
    protected function getCSVFile(): string
    {
        return base_path('database/seeds/csv/{{ $table }}.csv');
    }