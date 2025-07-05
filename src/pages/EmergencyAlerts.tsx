import React, { useState } from 'react';
import { AlertTriangle, Phone, Users, Clock, MapPin, Heart, Shield } from 'lucide-react';

const EmergencyAlerts: React.FC = () => {
  const [alertType, setAlertType] = useState('all');

  const alerts = [
    {
      id: 1,
      type: 'health',
      severity: 'high',
      title: 'Heart Rate Alert',
      message: 'Heart rate elevated above normal range (>100 bpm)',
      time: '2 hours ago',
      status: 'active',
      location: 'Home'
    },
    {
      id: 2,
      type: 'location',
      severity: 'medium',
      title: 'Geofence Exit',
      message: 'Left safe zone at Main Street Pharmacy',
      time: '4 hours ago',
      status: 'resolved',
      location: 'Pharmacy'
    },
    {
      id: 3,
      type: 'medication',
      severity: 'low',
      title: 'Missed Medication',
      message: 'Evening Metformin dose not taken',
      time: '1 day ago',
      status: 'resolved',
      location: 'Home'
    },
    {
      id: 4,
      type: 'device',
      severity: 'medium',
      title: 'Low Battery',
      message: 'Device battery below 20%',
      time: '2 days ago',
      status: 'resolved',
      location: 'Home'
    }
  ];

  const emergencyContacts = [
    { name: 'Sarah Thompson', relation: 'Daughter', phone: '+1 (555) 123-4567', priority: 1 },
    { name: 'Dr. Michael Roberts', relation: 'Primary Care', phone: '+1 (555) 987-6543', priority: 2 },
    { name: 'John Thompson', relation: 'Son', phone: '+1 (555) 456-7890', priority: 3 },
    { name: 'Emergency Services', relation: '911', phone: '911', priority: 4 },
  ];

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'high': return 'text-vita-coral bg-vita-coral bg-opacity-10 border-vita-coral border-opacity-30';
      case 'medium': return 'text-orange-600 bg-orange-50 border-orange-200';
      case 'low': return 'text-vita-blue bg-vita-blue bg-opacity-10 border-vita-blue border-opacity-30';
      default: return 'text-gray-600 bg-gray-50 border-gray-200';
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'health': return Heart;
      case 'location': return MapPin;
      case 'medication': return Clock;
      case 'device': return Shield;
      default: return AlertTriangle;
    }
  };

  const filteredAlerts = alertType === 'all' ? alerts : alerts.filter(alert => alert.type === alertType);

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Emergency Alerts</h1>
        <p className="text-gray-600 mt-2">Monitor and manage emergency situations</p>
      </div>

      {/* Emergency Action Button */}
      <div className="bg-vita-coral bg-opacity-10 border border-vita-coral border-opacity-30 rounded-2xl p-6 mb-8">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <AlertTriangle className="h-8 w-8 text-vita-coral" />
            <div>
              <h2 className="text-lg font-semibold text-vita-coral">Emergency Response</h2>
              <p className="text-sm text-vita-coral">Immediate assistance and emergency contacts</p>
            </div>
          </div>
          <button className="bg-vita-coral hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-medium transition-colors">
            Trigger Emergency Alert
          </button>
        </div>
      </div>

      {/* Alert Filter */}
      <div className="mb-8">
        <div className="flex space-x-1 bg-vita-grey-light p-1 rounded-2xl w-fit">
          {['all', 'health', 'location', 'medication', 'device'].map((type) => (
            <button
              key={type}
              onClick={() => setAlertType(type)}
              className={`px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 ${
                alertType === type
                  ? 'bg-white text-gray-900 shadow-soft'
                  : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              {type.charAt(0).toUpperCase() + type.slice(1)}
            </button>
          ))}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Recent Alerts */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Recent Alerts</h2>
            <div className="flex items-center space-x-2">
              <div className="w-2 h-2 bg-vita-coral rounded-full animate-pulse"></div>
              <span className="text-sm text-vita-coral font-medium">1 Active</span>
            </div>
          </div>
          
          <div className="space-y-4">
            {filteredAlerts.map((alert) => {
              const IconComponent = getTypeIcon(alert.type);
              return (
                <div key={alert.id} className={`p-4 rounded-2xl border ${getSeverityColor(alert.severity)}`}>
                  <div className="flex items-start space-x-3">
                    <IconComponent className="h-5 w-5 mt-0.5" />
                    <div className="flex-1">
                      <div className="flex items-center justify-between">
                        <h3 className="font-medium">{alert.title}</h3>
                        <span className={`text-xs px-2 py-1 rounded-full ${
                          alert.status === 'active' 
                            ? 'bg-vita-coral bg-opacity-20 text-vita-coral' 
                            : 'bg-vita-mint bg-opacity-20 text-vita-mint-dark'
                        }`}>
                          {alert.status}
                        </span>
                      </div>
                      <p className="text-sm mt-1">{alert.message}</p>
                      <div className="flex items-center space-x-4 mt-2 text-xs">
                        <span>{alert.time}</span>
                        <span>• {alert.location}</span>
                      </div>
                    </div>
                  </div>
                  {alert.status === 'active' && (
                    <div className="mt-3 flex items-center space-x-4">
                      <button className="text-sm text-vita-blue hover:text-vita-blue-dark font-medium transition-colors">
                        Acknowledge
                      </button>
                      <button className="text-sm text-vita-mint hover:text-vita-mint-dark font-medium transition-colors">
                        Resolve
                      </button>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Emergency Contacts */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Emergency Contacts</h2>
            <Users className="h-5 w-5 text-gray-400" />
          </div>
          
          <div className="space-y-4">
            {emergencyContacts.map((contact, index) => (
              <div key={index} className="p-4 bg-vita-white rounded-xl border border-vita-grey-light">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="font-medium text-gray-900">{contact.name}</p>
                    <p className="text-sm text-gray-600">{contact.relation}</p>
                    <p className="text-sm text-gray-500 mt-1">{contact.phone}</p>
                  </div>
                  <div className="flex items-center space-x-2">
                    <span className="text-xs px-2 py-1 bg-vita-blue bg-opacity-20 text-vita-blue rounded-full">
                      Priority {contact.priority}
                    </span>
                    <button className="p-2 text-vita-mint hover:text-vita-mint-dark hover:bg-vita-mint hover:bg-opacity-10 rounded-xl transition-colors">
                      <Phone className="h-4 w-4" />
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
          
          <button className="w-full mt-4 p-3 border border-vita-grey-light rounded-2xl text-sm font-medium text-gray-700 hover:bg-vita-grey-light transition-colors">
            + Add Emergency Contact
          </button>
        </div>
      </div>

      {/* Emergency Protocols */}
      <div className="mt-8 bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Emergency Protocols</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <div className="flex items-center space-x-2 mb-3">
              <Heart className="h-5 w-5 text-vita-coral" />
              <h3 className="font-medium text-gray-900">Health Emergency</h3>
            </div>
            <p className="text-sm text-gray-600 mb-3">Automated response for critical health alerts</p>
            <div className="text-xs text-gray-500 space-y-1">
              <p>• Notify emergency contacts</p>
              <p>• Alert medical professionals</p>
              <p>• Activate GPS tracking</p>
            </div>
          </div>
          
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <div className="flex items-center space-x-2 mb-3">
              <MapPin className="h-5 w-5 text-orange-500" />
              <h3 className="font-medium text-gray-900">Location Emergency</h3>
            </div>
            <p className="text-sm text-gray-600 mb-3">Response for wandering or unsafe locations</p>
            <div className="text-xs text-gray-500 space-y-1">
              <p>• Send location to contacts</p>
              <p>• Activate anti-kidnapping protocol</p>
              <p>• Enable two-way communication</p>
            </div>
          </div>
          
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <div className="flex items-center space-x-2 mb-3">
              <Shield className="h-5 w-5 text-vita-blue" />
              <h3 className="font-medium text-gray-900">Device Emergency</h3>
            </div>
            <p className="text-sm text-gray-600 mb-3">Protocol for device malfunction or removal</p>
            <div className="text-xs text-gray-500 space-y-1">
              <p>• Backup communication methods</p>
              <p>• Alert caregivers immediately</p>
              <p>• Activate secondary tracking</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EmergencyAlerts;