<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test: Helper Functions
 *
 * Test pure functions tanpa dependencies
 *
 * Command untuk menjalankan:
 * php artisan test --filter HelperFunctionsTest
 * php artisan test tests/Unit/Helpers/HelperFunctionsTest.php
 */
class HelperFunctionsTest extends TestCase
{
    /**
     * Test Case 1: Format NIM dengan benar
     *
     * Input: '2141720001'
     * Output: '2141720001' (10 digit)
     */
    public function test_format_nim_with_valid_input()
    {
        // Arrange
        $nim = '2141720001';

        // Act
        $result = $this->formatNIM($nim);

        // Assert
        $this->assertEquals('2141720001', $result);
        $this->assertEquals(10, strlen($result));
    }

    /**
     * Test Case 2: Calculate IPK average
     *
     * Input: [3.5, 3.7, 3.9, 4.0]
     * Output: 3.775
     */
    public function test_calculate_ipk_average()
    {
        // Arrange
        $ipkList = [3.5, 3.7, 3.9, 4.0];

        // Act
        $average = array_sum($ipkList) / count($ipkList);

        // Assert
        $this->assertEquals(3.775, $average);
    }

    /**
     * Test Case 3: Validate email format
     */
    public function test_validate_email_format()
    {
        // Valid emails
        $this->assertTrue(filter_var('student@example.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertTrue(filter_var('user.name@domain.co.id', FILTER_VALIDATE_EMAIL) !== false);

        // Invalid emails
        $this->assertFalse(filter_var('invalid-email', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('@domain.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('user@', FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * Test Case 4: Generate random password
     *
     * Expected:
     * - Length = 8 characters
     * - Contains uppercase, lowercase, numbers
     */
    public function test_generate_random_password()
    {
        // Act
        $password = $this->generateRandomPassword(8);

        // Assert
        $this->assertEquals(8, strlen($password));
        $this->assertMatchesRegularExpression('/[A-Z]/', $password); // Ada uppercase
        $this->assertMatchesRegularExpression('/[a-z]/', $password); // Ada lowercase
        $this->assertMatchesRegularExpression('/[0-9]/', $password); // Ada number
    }

    /**
     * Test Case 5: Sanitize user input
     *
     * Input: '<script>alert("XSS")</script>'
     * Output: 'alert("XSS")' (tanpa tags)
     */
    public function test_sanitize_user_input()
    {
        // Arrange
        $dirtyInput = '<script>alert("XSS")</script>';

        // Act
        $cleanInput = strip_tags($dirtyInput);

        // Assert
        $this->assertEquals('alert("XSS")', $cleanInput);
        $this->assertStringNotContainsString('<script>', $cleanInput);
    }

    /**
     * Test Case 6: Calculate percentage
     *
     * Input: 45 dari 150
     * Output: 30%
     */
    public function test_calculate_percentage()
    {
        // Arrange
        $part = 45;
        $total = 150;

        // Act
        $percentage = ($part / $total) * 100;

        // Assert
        $this->assertEquals(30, $percentage);
    }

    /**
     * Test Case 7: Check if string contains substring (case insensitive)
     */
    public function test_string_contains_case_insensitive()
    {
        // Arrange
        $haystack = 'Full Stack Developer';
        $needle = 'STACK';

        // Act
        $result = stripos($haystack, $needle) !== false;

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test Case 8: Convert array to comma separated string
     */
    public function test_array_to_comma_separated_string()
    {
        // Arrange
        $skills = ['PHP', 'JavaScript', 'MySQL', 'Laravel'];

        // Act
        $result = implode(', ', $skills);

        // Assert
        $this->assertEquals('PHP, JavaScript, MySQL, Laravel', $result);
    }

    /**
     * Test Case 9: Check if value is within range
     */
    public function test_value_is_within_range()
    {
        // IPK harus antara 0.0 - 4.0
        $this->assertTrue($this->isWithinRange(3.5, 0.0, 4.0));
        $this->assertTrue($this->isWithinRange(4.0, 0.0, 4.0)); // Boundary
        $this->assertFalse($this->isWithinRange(4.5, 0.0, 4.0)); // Out of range
        $this->assertFalse($this->isWithinRange(-1.0, 0.0, 4.0)); // Negative
    }

    /**
     * Test Case 10: Round number to 2 decimal places
     */
    public function test_round_number_to_decimal_places()
    {
        // Arrange
        $number = 3.14159;

        // Act
        $rounded = round($number, 2);

        // Assert
        $this->assertEquals(3.14, $rounded);
    }

    // ==================== Helper Methods ====================

    /**
     * Format NIM helper
     */
    private function formatNIM(string $nim): string
    {
        return str_pad($nim, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate random password helper
     */
    private function generateRandomPassword(int $length): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $all = $uppercase . $lowercase . $numbers;

        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];

        for ($i = 3; $i < $length; $i++) {
            $password .= $all[rand(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Check if value is within range
     */
    private function isWithinRange($value, $min, $max): bool
    {
        return $value >= $min && $value <= $max;
    }
}
