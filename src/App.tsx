import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import HealthMetrics from './pages/HealthMetrics';
import LocationTracking from './pages/LocationTracking';
import MedicationManager from './pages/MedicationManager';
import EmergencyAlerts from './pages/EmergencyAlerts';
import ProfileSettings from './pages/ProfileSettings';
import DeviceSetup from './pages/DeviceSetup';

function App() {
  return (
    <Router>
      <Layout>
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/health" element={<HealthMetrics />} />
          <Route path="/location" element={<LocationTracking />} />
          <Route path="/medication" element={<MedicationManager />} />
          <Route path="/alerts" element={<EmergencyAlerts />} />
          <Route path="/profile" element={<ProfileSettings />} />
          <Route path="/setup" element={<DeviceSetup />} />
        </Routes>
      </Layout>
    </Router>
  );
}

export default App;