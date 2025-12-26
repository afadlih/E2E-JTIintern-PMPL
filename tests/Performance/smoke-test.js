/**
 * K6 Smoke Testing - Quick Validation
 * 
 * Purpose: Quick test untuk verify sistem berjalan normal
 * Simulates: 1-2 users untuk 30 detik
 * Target: https://afws.my.id
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';

// Smoke test - minimal load
export const options = {
  vus: 1,           // 1 virtual user
  duration: '30s',  // 30 seconds
  thresholds: {
    http_req_duration: ['p(95)<3000'], // 3s threshold for production
    http_req_failed: ['rate<0.2'],     // Allow 20% failures
  },
};

const BASE_URL = 'https://afws.my.id/E2E-JTIintern-PMPL';

export function setup() {
  console.log('🔍 Starting Smoke Test');
  console.log(`📍 Target: ${BASE_URL}`);
  console.log('👥 Users: 1 (minimal)');
  console.log('⏱️  Duration: 30 seconds');
  console.log('🎯 Goal: Verify basic functionality');
}

export default function () {
  // Test 1: Homepage/Login Page
  let loginRes = http.get(`${BASE_URL}/login`);
  check(loginRes, {
    'Login page - status is 200': (r) => r.status === 200,
    'Login page - loads in < 3s': (r) => r.timings.duration < 3000,
    'Login page - has login form': (r) => r.body.includes('email') || r.body.includes('password') || r.body.includes('login'),
  });

  sleep(1);

  // Test 2: Try to access a public page
  let publicRes = http.get(`${BASE_URL}/`);
  check(publicRes, {
    'Homepage - responds': (r) => r.status === 200 || r.status === 302,
  });

  sleep(2);
}

export function teardown(data) {
  console.log('✅ Smoke Test Completed');
  console.log('✓ Basic functionality verified');
}
