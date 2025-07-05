import React, { useState } from 'react';
import { MapPin, Home, AlertTriangle, Clock, Shield, Navigation } from 'lucide-react';

const LocationTracking: React.FC = () => {
  const [activeZone, setActiveZone] = useState('home');

  const safeZones = [
    { id: 'home', name: 'Home', address: '123 Oak Street, Springfield', radius: '50 meters', active: true },
    { id: 'pharmacy', name: 'Pharmacy', address: '456 Main Street, Springfield', radius: '25 meters', active: true },
    { id: 'hospital', name: 'Hospital', address: '789 Medical Drive, Springfield', radius: '100 meters', active: true },
  ];

  const locationHistory = [
    { time: '2:45 PM', location: 'Home', status: 'safe', duration: '45 min' },
    { time: '1:30 PM', location: 'Pharmacy', status: 'safe', duration: '15 min' },
    { time: '12:00 PM', location: 'Walking Route', status: 'safe', duration: '30 min' },
    { time: '11:15 AM', location: 'Home', status: 'safe', duration: '3 hours' },
  ];

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Location Tracking</h1>
        <p className="text-gray-600 mt-2">Real-time location monitoring and safety zones</p>
      </div>

      {/* Current Location */}
      <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6 mb-8">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-xl font-semibold text-gray-900">Current Location</h2>
          <div className="flex items-center space-x-2">
            <div className="w-2 h-2 bg-vita-mint rounded-full animate-pulse"></div>
            <span className="text-sm text-vita-mint font-medium">Live</span>
          </div>
        </div>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Map Placeholder */}
          <div className="h-64 bg-gradient-to-br from-vita-blue from-opacity-5 to-vita-mint to-opacity-10 rounded-2xl border border-vita-grey-light flex items-center justify-center">
            <div className="text-center">
              <MapPin className="h-12 w-12 text-vita-blue mx-auto mb-2" />
              <p className="text-sm font-medium text-gray-700">Interactive Map View</p>
              <p className="text-xs text-gray-500 mt-1">123 Oak Street, Springfield</p>
            </div>
          </div>

          {/* Location Details */}
          <div className="space-y-4">
            <div className="flex items-center space-x-3 p-3 bg-vita-mint bg-opacity-10 rounded-2xl border border-vita-mint border-opacity-30">
              <Home className="h-5 w-5 text-vita-mint-dark" />
              <div>
                <p className="text-sm font-medium text-vita-mint-dark">Currently at Home</p>
                <p className="text-xs text-vita-mint">Safe Zone • Last updated 2 min ago</p>
              </div>
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="p-3 bg-vita-white rounded-xl border border-vita-grey-light">
                <p className="text-xs text-gray-600">Coordinates</p>
                <p className="text-sm font-medium text-gray-900">39.7392, -104.9903</p>
              </div>
              <div className="p-3 bg-vita-white rounded-xl border border-vita-grey-light">
                <p className="text-xs text-gray-600">Accuracy</p>
                <p className="text-sm font-medium text-gray-900">±3 meters</p>
              </div>
            </div>

            <div className="p-3 bg-vita-blue bg-opacity-10 rounded-2xl border border-vita-blue border-opacity-30">
              <div className="flex items-center space-x-2">
                <Shield className="h-4 w-4 text-vita-blue" />
                <p className="text-sm font-medium text-vita-blue-dark">All safety protocols active</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Safe Zones */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Safe Zones</h2>
            <button className="text-sm font-medium text-vita-blue hover:text-vita-blue-dark transition-colors">
              + Add Zone
            </button>
          </div>
          
          <div className="space-y-4">
            {safeZones.map((zone) => (
              <div key={zone.id} className="p-4 border border-vita-grey-light rounded-2xl">
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center space-x-2">
                    <div className={`w-3 h-3 rounded-full ${zone.active ? 'bg-vita-mint' : 'bg-vita-grey'}`} />
                    <span className="font-medium text-gray-900">{zone.name}</span>
                  </div>
                  <span className="text-xs text-gray-500">{zone.radius}</span>
                </div>
                <p className="text-sm text-gray-600">{zone.address}</p>
                <div className="mt-2 flex items-center space-x-4">
                  <button className="text-xs text-vita-blue hover:text-vita-blue-dark transition-colors">Edit</button>
                  <button className="text-xs text-vita-coral hover:text-red-700 transition-colors">Remove</button>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Location History */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Location History</h2>
            <Clock className="h-5 w-5 text-gray-400" />
          </div>
          
          <div className="space-y-4">
            {locationHistory.map((entry, index) => (
              <div key={index} className="flex items-center space-x-3 p-3 bg-vita-white rounded-xl border border-vita-grey-light">
                <div className="flex-shrink-0">
                  <div className={`w-2 h-2 rounded-full ${
                    entry.status === 'safe' ? 'bg-vita-mint' : 'bg-vita-coral'
                  }`} />
                </div>
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-gray-900">{entry.location}</span>
                    <span className="text-xs text-gray-500">{entry.time}</span>
                  </div>
                  <p className="text-xs text-gray-600">Duration: {entry.duration}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Emergency Settings */}
      <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <div className="flex items-center space-x-2 mb-6">
          <AlertTriangle className="h-5 w-5 text-vita-coral" />
          <h2 className="text-xl font-semibold text-gray-900">Emergency & Safety Settings</h2>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <h3 className="text-sm font-medium text-gray-900 mb-2">Geofence Alerts</h3>
            <p className="text-xs text-gray-600 mb-3">Get notified when leaving safe zones</p>
            <label className="flex items-center">
              <input type="checkbox" className="rounded border-gray-300" defaultChecked />
              <span className="ml-2 text-sm text-gray-700">Enable alerts</span>
            </label>
          </div>
          
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <h3 className="text-sm font-medium text-gray-900 mb-2">Emergency Contacts</h3>
            <p className="text-xs text-gray-600 mb-3">Auto-notify in case of emergencies</p>
            <button className="text-sm text-vita-blue hover:text-vita-blue-dark transition-colors">
              Manage Contacts
            </button>
          </div>
          
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <h3 className="text-sm font-medium text-gray-900 mb-2">Check-in Reminders</h3>
            <p className="text-xs text-gray-600 mb-3">Regular safety check-ins</p>
            <label className="flex items-center">
              <input type="checkbox" className="rounded border-gray-300" defaultChecked />
              <span className="ml-2 text-sm text-gray-700">Enable reminders</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LocationTracking;