<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    public function getImageUrl(): ?string
    {
        if (!$this->image) return null;
        return (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/'))
            ? $this->image
            : asset('storage/images/slides/' . $this->image);
    }
}
