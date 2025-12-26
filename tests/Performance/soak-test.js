/**
 * K6 Soak Testing - Endurance Test
 * 
 * Purpose: Test stability sistem dalam jangka waktu lama
 * Simulates: 20 users selama 10 menit untuk detect memory leaks
 * Target: https://afws.my.id
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');
const memoryLeakIndicator = new Trend('response_time_trend');
const totalIterations = new Counter('total_iterations');

// Soak test configuration - Long duration
export const options = {
  stages: [
    { duration: '30s', target: 10 },  // Ramp up
    { duration: '4m', target: 10 },   // Soak - maintain load
    { duration: '30s', target: 0 },   // Ramp down
  ],
  thresholds: {
    http_req_duration: ['p(95)<10000'], // 10s threshold
    http_req_failed: ['rate<0.3'],      // Allow 30% failure
  },
};

const BASE_URL = 'https://afws.my.id/E2E-JTIintern-PMPL';

export function setup() {
  console.log('🔥 Starting Soak Test (Endurance)');
  console.log(`📍 Target: ${BASE_URL}`);
  console.log('👥 Concurrent Users: 10 (sustained)');
  console.log('⏱️  Duration: 5 minutes');
  console.log('🎯 Goal: Detect response degradation');
  
  return { 
    startTime: Date.now(),
    firstResponseTime: null,
  };
}

export default function (data) {
  totalIterations.add(1);
  
  // Test: Login page sustained load
  let loginPage = http.get(`${BASE_URL}/login`, {
    timeout: '60s',
  });
  
  const success = check(loginPage, {
    'Login page loads': (r) => r.status === 200 || r.status === 302,
    'No timeout': (r) => r.timings.duration < 60000,
  });
  
  if (!success) {
    errorRate.add(1);
  }
  
  memoryLeakIndicator.add(loginPage.timings.duration);
  
  sleep(3);
  
  // Test homepage
  let homePage = http.get(`${BASE_URL}/`, {
    timeout: '60s',
  });
  
  check(homePage, {
    'Homepage loads': (r) => r.status === 200 || r.status === 302,
  });
  
  sleep(2);
  
  // Log progress every 20 iterations
  if (totalIterations.value % 20 === 0) {
    const elapsed = (Date.now() - data.startTime) / 1000 / 60;
    console.log(`⏱️  ${elapsed.toFixed(1)} minutes elapsed, ${totalIterations.value} iterations completed`);
  }
}

export function teardown(data) {
  const totalTime = (Date.now() - data.startTime) / 1000 / 60;
  console.log('✅ Soak Test Completed');
  console.log(`⏱️  Total Time: ${totalTime.toFixed(2)} minutes`);
  console.log('📊 Check for response time degradation');
}
