/**
 * K6 Stress Testing - High Load Test
 * 
 * Purpose: Test website dengan beban tinggi untuk menemukan breaking point
 * Simulates: Gradual increase sampai 100 concurrent users
 * Target: https://afws.my.id
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');
const successRate = new Rate('success');
const totalRequests = new Counter('total_requests');
const pageDuration = new Trend('page_load_duration');

// Stress test configuration
export const options = {
  stages: [
    { duration: '1m', target: 10 },   // Ramp up ke 10 users
    { duration: '2m', target: 20 },   // Increase ke 20 users
    { duration: '2m', target: 30 },   // Increase ke 30 users (stress)
    { duration: '2m', target: 20 },   // Scale down ke 20
    { duration: '1m', target: 0 },    // Recovery
  ],
  thresholds: {
    http_req_duration: ['p(95)<10000'], // 95% requests harus < 10s
    http_req_failed: ['rate<0.5'],      // Error rate bisa sampai 50% di stress
  },
};

const BASE_URL = 'https://afws.my.id/E2E-JTIintern-PMPL';

export function setup() {
  console.log('💥 Starting Stress Test');
  console.log(`📍 Target: ${BASE_URL}`);
  console.log('👥 Max Users: 30 concurrent');
  console.log('⏱️  Duration: 8 minutes');
  console.log('⚠️  Warning: This will stress test the server!');
  
  // Warm-up request
  const warmup = http.get(`${BASE_URL}/login`);
  console.log(`🌡️  Initial response time: ${warmup.timings.duration}ms`);
  
  return { startTime: Date.now() };
}

export default function (data) {
  totalRequests.add(1);
  
  // Test: Login Page under stress
  let loginRes = http.get(`${BASE_URL}/login`, {
    tags: { page: 'login' },
    timeout: '60s', // Increase timeout
  });
  
  const loginSuccess = check(loginRes, {
    'Login page responds': (r) => r.status === 200 || r.status === 302,
    'No timeout': (r) => r.timings.duration < 60000,
  });
  
  if (loginSuccess) {
    successRate.add(1);
    pageDuration.add(loginRes.timings.duration);
  } else {
    errorRate.add(1);
  }

  sleep(Math.random() * 3 + 2); // Random 2-5 seconds

  // Test homepage
  let homeRes = http.get(`${BASE_URL}/`, {
    timeout: '60s',
  });
  
  check(homeRes, {
    'Homepage responds': (r) => r.status === 200 || r.status === 302,
  });

  sleep(Math.random() * 2 + 1); // Random 1-3 seconds
}

export function teardown(data) {
  const duration = (Date.now() - data.startTime) / 1000;
  console.log('✅ Stress Test Completed');
  console.log(`⏱️  Total Duration: ${duration.toFixed(2)} seconds`);
  console.log('📊 Check the results for breaking points');
}
