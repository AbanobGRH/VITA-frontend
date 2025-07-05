import React from 'react';
import { Heart, MapPin, Pill, AlertTriangle, Battery, Wifi, Thermometer, Activity } from 'lucide-react';

const Dashboard: React.FC = () => {
  const vitalSigns = [
    { name: 'Heart Rate', value: '72', unit: 'bpm', status: 'normal', icon: Heart, color: 'text-vita-coral', bgColor: 'bg-vita-coral bg-opacity-10' },
    { name: 'Blood Pressure', value: '120/80', unit: 'mmHg', status: 'normal', icon: Activity, color: 'text-vita-blue', bgColor: 'bg-vita-blue bg-opacity-10' },
    { name: 'Temperature', value: '98.6', unit: '°F', status: 'normal', icon: Thermometer, color: 'text-orange-500', bgColor: 'bg-orange-50' },
    { name: 'Steps Today', value: '4,235', unit: 'steps', status: 'good', icon: Activity, color: 'text-vita-mint', bgColor: 'bg-vita-mint bg-opacity-10' },
  ];

  const alerts = [
    { id: 1, type: 'medication', message: 'Blood pressure medication due in 30 minutes', time: '2:30 PM', priority: 'medium' },
    { id: 2, type: 'health', message: 'Heart rate slightly elevated during morning walk', time: '10:15 AM', priority: 'low' },
  ];

  return (
    <div className="p-6 max-w-7xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Health Dashboard</h1>
        <p className="text-gray-600 mt-2">Real-time monitoring for Margaret Thompson</p>
      </div>

      {/* Status Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {vitalSigns.map((vital) => {
          const Icon = vital.icon;
          return (
            <div key={vital.name} className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6 hover:shadow-soft-lg transition-all duration-300">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{vital.name}</p>
                  <p className="text-2xl font-bold text-gray-900 mt-1">
                    {vital.value}
                    <span className="text-sm font-normal text-gray-500 ml-1">{vital.unit}</span>
                  </p>
                  <p className={`text-xs font-medium mt-1 ${vital.color}`}>
                    {vital.status === 'normal' ? '● Normal' : vital.status === 'good' ? '● Good' : '● Attention'}
                  </p>
                </div>
                <div className={`p-3 rounded-2xl ${vital.bgColor}`}>
                  <Icon className={`h-6 w-6 ${vital.color}`} />
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Recent Alerts */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Recent Alerts</h2>
            <AlertTriangle className="h-5 w-5 text-gray-400" />
          </div>
          <div className="space-y-4">
            {alerts.map((alert) => (
              <div key={alert.id} className="flex items-start space-x-3 p-4 bg-vita-white rounded-xl border border-vita-grey-light">
                <div className={`p-1 rounded-full ${
                  alert.priority === 'high' ? 'bg-vita-coral bg-opacity-20' : 
                  alert.priority === 'medium' ? 'bg-orange-100' : 'bg-vita-blue bg-opacity-20'
                }`}>
                  <div className={`w-2 h-2 rounded-full ${
                    alert.priority === 'high' ? 'bg-vita-coral' : 
                    alert.priority === 'medium' ? 'bg-orange-500' : 'bg-vita-blue'
                  }`} />
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">{alert.message}</p>
                  <p className="text-xs text-gray-500 mt-1">{alert.time}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Device Status */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Device Status</h2>
            <div className="flex items-center space-x-2">
              <div className="w-2 h-2 bg-vita-mint rounded-full animate-pulse"></div>
              <span className="text-sm text-vita-mint font-medium">Online</span>
            </div>
          </div>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-vita-white rounded-xl border border-vita-grey-light">
              <div className="flex items-center space-x-3">
                <Battery className="h-5 w-5 text-vita-mint" />
                <span className="text-sm font-medium text-gray-900">Battery Level</span>
              </div>
              <div className="flex items-center space-x-2">
                <div className="w-16 h-2 bg-vita-grey-light rounded-full">
                  <div className="w-14 h-full bg-vita-mint rounded-full"></div>
                </div>
                <span className="text-sm font-medium text-gray-900">87%</span>
              </div>
            </div>
            <div className="flex items-center justify-between p-4 bg-vita-white rounded-xl border border-vita-grey-light">
              <div className="flex items-center space-x-3">
                <Wifi className="h-5 w-5 text-vita-blue" />
                <span className="text-sm font-medium text-gray-900">Connection</span>
              </div>
              <span className="text-sm font-medium text-vita-mint">Strong</span>
            </div>
            <div className="flex items-center justify-between p-4 bg-vita-white rounded-xl border border-vita-grey-light">
              <div className="flex items-center space-x-3">
                <MapPin className="h-5 w-5 text-orange-500" />
                <span className="text-sm font-medium text-gray-900">Last Location</span>
              </div>
              <span className="text-sm text-gray-600">Home • 2 min ago</span>
            </div>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="mt-8 bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <button className="flex items-center justify-center space-x-2 p-4 bg-vita-coral bg-opacity-10 hover:bg-vita-coral hover:bg-opacity-20 rounded-2xl transition-all duration-300">
            <AlertTriangle className="h-5 w-5 text-vita-coral" />
            <span className="text-sm font-medium text-vita-coral">Emergency Alert</span>
          </button>
          <button className="flex items-center justify-center space-x-2 p-4 bg-vita-blue bg-opacity-10 hover:bg-vita-blue hover:bg-opacity-20 rounded-2xl transition-all duration-300">
            <Pill className="h-5 w-5 text-vita-blue" />
            <span className="text-sm font-medium text-vita-blue">Add Medication</span>
          </button>
          <button className="flex items-center justify-center space-x-2 p-4 bg-vita-mint bg-opacity-20 hover:bg-vita-mint hover:bg-opacity-30 rounded-2xl transition-all duration-300">
            <MapPin className="h-5 w-5 text-vita-mint-dark" />
            <span className="text-sm font-medium text-vita-mint-dark">Set Safe Zone</span>
          </button>
          <button className="flex items-center justify-center space-x-2 p-4 bg-purple-50 hover:bg-purple-100 rounded-2xl transition-all duration-300">
            <Heart className="h-5 w-5 text-purple-600" />
            <span className="text-sm font-medium text-purple-700">Health Report</span>
          </button>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;