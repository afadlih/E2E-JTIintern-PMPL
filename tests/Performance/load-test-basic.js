/**
 * K6 Load Testing - Basic Load Test
 * 
 * Purpose: Test website dengan beban normal
 * Simulates: 10 concurrent users untuk 1 menit
 * Target: https://afws.my.id
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');
const loginDuration = new Trend('login_duration');
const pageDuration = new Trend('page_duration');

// Test configuration
export const options = {
  stages: [
    { duration: '30s', target: 10 },  // Ramp up ke 10 users
    { duration: '1m', target: 10 },   // Stay di 10 users
    { duration: '30s', target: 0 },   // Ramp down ke 0
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000'], // 95% requests harus < 3s
    http_req_failed: ['rate<0.2'],     // Error rate harus < 20%
  },
};

const BASE_URL = 'https://afws.my.id/E2E-JTIintern-PMPL';

export function setup() {
  console.log('🚀 Starting Load Test - Basic');
  console.log(`📍 Target: ${BASE_URL}`);
  console.log('👥 Simulating: 10 concurrent users');
  console.log('⏱️  Duration: 2 minutes');
}

export default function () {
  // Test 1: Login Page Load
  let loginRes = http.get(`${BASE_URL}/login`);
  check(loginRes, {
    'Login page loaded': (r) => r.status === 200,
    'Login page response time OK': (r) => r.timings.duration < 3000,
  });
  
  loginDuration.add(loginRes.timings.duration);

  sleep(1);

  // Test 2: Homepage
  let homeRes = http.get(`${BASE_URL}/`);
  check(homeRes, {
    'Homepage accessible': (r) => r.status === 200 || r.status === 302,
  });
  
  pageDuration.add(homeRes.timings.duration);

  sleep(2);
}

export function teardown(data) {
  console.log('✅ Load Test Completed');
}
