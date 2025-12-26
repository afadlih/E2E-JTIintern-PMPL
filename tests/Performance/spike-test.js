/**
 * K6 Spike Testing - Sudden Traffic Spike
 * 
 * Purpose: Test bagaimana sistem handle sudden traffic spike
 * Simulates: Sudden spike dari 0 ke 200 users
 * Target: https://afws.my.id
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');
const recoveryTime = new Trend('recovery_time');

// Spike test configuration
export const options = {
  stages: [
    { duration: '10s', target: 5 },    // Normal load
    { duration: '20s', target: 50 },   // SPIKE to 50 users
    { duration: '1m', target: 50 },    // Maintain spike
    { duration: '20s', target: 5 },    // Quick recovery
    { duration: '30s', target: 0 },    // Cool down
  ],
  thresholds: {
    http_req_duration: ['p(95)<60000'], // Very lenient 60s during spike
    http_req_failed: ['rate<0.7'],      // Allow 70% failure during spike
  },
};

const BASE_URL = 'https://afws.my.id/E2E-JTIintern-PMPL';

export function setup() {
  console.log('⚡ Starting Spike Test');
  console.log(`📍 Target: ${BASE_URL}`);
  console.log('👥 Spike: 0 → 50 users in 20 seconds!');
  console.log('⏱️  Duration: ~2 minutes');
}

export default function () {
  const startTime = Date.now();
  
  // Rapid fire requests during spike
  let res = http.get(`${BASE_URL}/login`, {
    timeout: '60s',
  });
  
  const success = check(res, {
    'Response received': (r) => r.status === 200 || r.status === 302,
    'No timeout': (r) => r.timings.duration < 60000,
  });
  
  if (!success) {
    errorRate.add(1);
  }
  
  recoveryTime.add(Date.now() - startTime);
  
  // Minimal sleep during spike
  sleep(1);
}

export function teardown(data) {
  console.log('✅ Spike Test Completed');
  console.log('📊 Check if server recovered from traffic spike');
}
