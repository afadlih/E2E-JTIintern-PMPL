<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test: Pure Logic - Validation Functions
 *
 * Test fungsi-fungsi validasi tanpa dependency Laravel
 * Fokus pada pure logic testing
 *
 * Command untuk menjalankan:
 * php artisan test --filter ValidationLogicTest
 * php artisan test tests/Unit/Services/ValidationLogicTest.php
 */
class ValidationLogicTest extends TestCase
{
    /**
     * Test Case 1: Validate NIM format (10 digit numeric)
     */
    public function test_validate_nim_format_valid()
    {
        // Arrange
        $validNIM = '2141720001';

        // Act
        $isValid = $this->validateNIM($validNIM);

        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test Case 2: Validate NIM format invalid (kurang dari 10 digit)
     */
    public function test_validate_nim_format_invalid_kurang_10_digit()
    {
        // Arrange
        $invalidNIM = '214172';

        // Act
        $isValid = $this->validateNIM($invalidNIM);

        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test Case 3: Validate NIM format invalid (mengandung huruf)
     */
    public function test_validate_nim_format_invalid_dengan_huruf()
    {
        // Arrange
        $invalidNIM = '214172ABC1';

        // Act
        $isValid = $this->validateNIM($invalidNIM);

        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test Case 4: Validate IPK range (0.0 - 4.0)
     */
    public function test_validate_ipk_range_valid()
    {
        // Arrange
        $validIPK = [0.0, 2.5, 3.0, 3.75, 4.0];

        // Act & Assert
        foreach ($validIPK as $ipk) {
            $this->assertTrue($this->validateIPK($ipk));
        }
    }

    /**
     * Test Case 5: Validate IPK range invalid (di luar 0.0 - 4.0)
     */
    public function test_validate_ipk_range_invalid()
    {
        // Arrange
        $invalidIPK = [-1.0, 4.5, 5.0];

        // Act & Assert
        foreach ($invalidIPK as $ipk) {
            $this->assertFalse($this->validateIPK($ipk));
        }
    }

    /**
     * Test Case 6: Check mahasiswa eligible untuk apply (IPK >= 3.0)
     */
    public function test_check_mahasiswa_eligible_ipk_minimal()
    {
        // Arrange & Act & Assert
        $this->assertTrue($this->isEligibleToApply(3.0));
        $this->assertTrue($this->isEligibleToApply(3.5));
        $this->assertFalse($this->isEligibleToApply(2.9));
    }

    /**
     * Test Case 7: Calculate average rating
     */
    public function test_calculate_average_rating()
    {
        // Arrange
        $ratings = [4, 5, 3, 5, 4];

        // Act
        $average = $this->calculateAverage($ratings);

        // Assert
        $this->assertEquals(4.2, $average);
    }

    /**
     * Test Case 8: Calculate average dengan array kosong
     */
    public function test_calculate_average_array_kosong()
    {
        // Arrange
        $ratings = [];

        // Act
        $average = $this->calculateAverage($ratings);

        // Assert
        $this->assertEquals(0, $average);
    }

    /**
     * Test Case 9: Check capacity lowongan penuh
     */
    public function test_check_capacity_lowongan_penuh()
    {
        // Arrange
        $maxCapacity = 5;
        $currentApplicants = 5;

        // Act
        $isPenuh = $this->isCapacityFull($currentApplicants, $maxCapacity);

        // Assert
        $this->assertTrue($isPenuh);
    }

    /**
     * Test Case 10: Check capacity lowongan masih tersedia
     */
    public function test_check_capacity_lowongan_tersedia()
    {
        // Arrange
        $maxCapacity = 5;
        $currentApplicants = 3;

        // Act
        $isPenuh = $this->isCapacityFull($currentApplicants, $maxCapacity);

        // Assert
        $this->assertFalse($isPenuh);
    }

    // ============ Helper Methods (Pure Functions) ============

    /**
     * Validate NIM format (10 digit numeric)
     */
    private function validateNIM(string $nim): bool
    {
        return strlen($nim) === 10 && ctype_digit($nim);
    }

    /**
     * Validate IPK range (0.0 - 4.0)
     */
    private function validateIPK(float $ipk): bool
    {
        return $ipk >= 0.0 && $ipk <= 4.0;
    }

    /**
     * Check mahasiswa eligible untuk apply (IPK >= 3.0)
     */
    private function isEligibleToApply(float $ipk): bool
    {
        return $ipk >= 3.0;
    }

    /**
     * Calculate average rating
     */
    private function calculateAverage(array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        return array_sum($values) / count($values);
    }

    /**
     * Check capacity lowongan penuh
     */
    private function isCapacityFull(int $currentApplicants, int $maxCapacity): bool
    {
        return $currentApplicants >= $maxCapacity;
    }
}
