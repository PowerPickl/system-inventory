<?php
// File: app/Exports/SimpleArrayExport.php

namespace App\Exports;

class SimpleArrayExport
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Simple array to Excel conversion
     */
    public function array(): array
    {
        $result = [];
        
        // Add headers as first row
        $result[] = $this->data['headers'];
        
        // Add data rows
        foreach($this->data['rows'] as $row) {
            $result[] = $row;
        }
        
        return $result;
    }

    /**
     * Title for worksheet
     */
    public function title(): string
    {
        return $this->data['title'] ?? 'Export';
    }
}