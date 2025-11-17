<?php


namespace App\Services;

class AutomationConfigService
{
    private $environment;
    
    public function __construct()
    {
        $this->environment = config('app.env', 'production');
    }
    
    public function getInterval(): int
    {
        return config("automation.intervals.{$this->environment}", 3600);
    }
    
    public function getBatchSize(): int
    {
        return config("automation.batch_sizes.{$this->environment}", 20);
    }
    
    public function getCacheDuration(): int
    {
        return config("automation.cache_durations.{$this->environment}", 3600);
    }
    
    public function getRateLimit(): int
    {
        return config("automation.rate_limits.{$this->environment}", 30);
    }
    
    public function isBusinessHoursEnabled(): bool
    {
        return config('automation.business_hours.enabled', true) && 
               $this->environment === 'production';
    }
    
    public function getBusinessHours(): array
    {
        return [
            'start' => config('automation.business_hours.start', 8),
            'end' => config('automation.business_hours.end', 17)
        ];
    }
    
    public function isDebugMode(): bool
    {
        return in_array($this->environment, ['local', 'testing']);
    }
    
    public function getAllSettings(): array
    {
        return [
            'environment' => $this->environment,
            'interval' => $this->getInterval(),
            'batch_size' => $this->getBatchSize(),
            'cache_duration' => $this->getCacheDuration(),
            'rate_limit' => $this->getRateLimit(),
            'business_hours_enabled' => $this->isBusinessHoursEnabled(),
            'business_hours' => $this->getBusinessHours(),
            'debug_mode' => $this->isDebugMode()
        ];
    }
}