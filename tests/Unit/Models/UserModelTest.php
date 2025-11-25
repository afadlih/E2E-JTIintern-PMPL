<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
        /**
     * Test Case 4: Mahasiswa full name accessor (jika ada)
     *
     * Expected: Jika ada accessor getFullNameAttribute()
     */
    public function test_mahasiswa_full_name_accessor()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        // Act
        $fullName = $user->nama; 

        // Assert
        $this->assertEquals('John Doe', $fullName);
    }

}
