import React, { useState } from 'react';
import { User, Heart, MapPin, Phone, Mail, Calendar, Shield, Settings } from 'lucide-react';

const ProfileSettings: React.FC = () => {
  const [activeTab, setActiveTab] = useState('personal');

  const profileData = {
    name: 'Margaret Thompson',
    age: 78,
    email: 'margaret.thompson@email.com',
    phone: '+1 (555) 123-4567',
    address: '123 Oak Street, Springfield, IL 62701',
    emergencyContact: 'Sarah Thompson (Daughter)',
    medicalConditions: ['Hypertension', 'Type 2 Diabetes', 'Osteoarthritis'],
    allergies: ['Penicillin', 'Shellfish']
  };

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Profile Settings</h1>
        <p className="text-gray-600 mt-2">Manage personal information and preferences</p>
      </div>

      {/* Profile Header */}
      <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6 mb-8">
        <div className="flex items-center space-x-4">
          <div className="w-20 h-20 bg-gradient-to-br from-vita-blue to-vita-mint rounded-2xl flex items-center justify-center">
            <span className="text-2xl font-bold text-white">MT</span>
          </div>
          <div>
            <h2 className="text-2xl font-bold text-gray-900">{profileData.name}</h2>
            <p className="text-gray-600">Age {profileData.age} • Active since March 2024</p>
            <div className="flex items-center space-x-4 mt-2">
              <div className="flex items-center space-x-1">
                <div className="w-2 h-2 bg-vita-mint rounded-full"></div>
                <span className="text-sm text-vita-mint">Device Connected</span>
              </div>
              <div className="flex items-center space-x-1">
                <Shield className="h-4 w-4 text-vita-blue" />
                <span className="text-sm text-vita-blue">Protected</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="mb-8">
        <nav className="flex space-x-8">
          {['personal', 'medical', 'emergency', 'preferences'].map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`py-2 px-1 border-b-2 font-medium text-sm transition-colors ${
                activeTab === tab
                  ? 'border-vita-blue text-vita-blue'
                  : 'border-transparent text-gray-500 hover:text-gray-700'
              }`}
            >
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </button>
          ))}
        </nav>
      </div>

      {/* Tab Content */}
      <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        {activeTab === 'personal' && (
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-6">Personal Information</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input
                  type="text"
                  defaultValue={profileData.name}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Age</label>
                <input
                  type="number"
                  defaultValue={profileData.age}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  type="email"
                  defaultValue={profileData.email}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input
                  type="tel"
                  defaultValue={profileData.phone}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <input
                  type="text"
                  defaultValue={profileData.address}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
            </div>
          </div>
        )}

        {activeTab === 'medical' && (
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-6">Medical Information</h3>
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Medical Conditions</label>
                <div className="space-y-2">
                  {profileData.medicalConditions.map((condition, index) => (
                    <div key={index} className="flex items-center justify-between p-3 bg-vita-white rounded-xl border border-vita-grey-light">
                      <span className="text-sm text-gray-700">{condition}</span>
                      <button className="text-sm text-vita-coral hover:text-red-700 transition-colors">Remove</button>
                    </div>
                  ))}
                </div>
                <button className="mt-2 text-sm text-vita-blue hover:text-vita-blue-dark transition-colors">+ Add Condition</button>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                <div className="space-y-2">
                  {profileData.allergies.map((allergy, index) => (
                    <div key={index} className="flex items-center justify-between p-3 bg-vita-coral bg-opacity-10 rounded-xl border border-vita-coral border-opacity-30">
                      <span className="text-sm text-vita-coral">{allergy}</span>
                      <button className="text-sm text-vita-coral hover:text-red-700 transition-colors">Remove</button>
                    </div>
                  ))}
                </div>
                <button className="mt-2 text-sm text-vita-blue hover:text-vita-blue-dark transition-colors">+ Add Allergy</button>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Primary Care Physician</label>
                <input
                  type="text"
                  placeholder="Dr. Michael Roberts"
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Insurance Information</label>
                <textarea
                  placeholder="Insurance provider, policy number, group number..."
                  rows={3}
                  className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                />
              </div>
            </div>
          </div>
        )}

        {activeTab === 'emergency' && (
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-6">Emergency Contacts</h3>
            <div className="space-y-4">
              <div className="p-4 border border-vita-grey-light rounded-2xl">
                <div className="flex items-center justify-between mb-3">
                  <h4 className="font-medium text-gray-900">Primary Emergency Contact</h4>
                  <span className="text-xs px-2 py-1 bg-vita-blue bg-opacity-20 text-vita-blue rounded-full">Priority 1</span>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input
                      type="text"
                      defaultValue="Sarah Thompson"
                      className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                    <input
                      type="text"
                      defaultValue="Daughter"
                      className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input
                      type="tel"
                      defaultValue="+1 (555) 123-4567"
                      className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                      type="email"
                      defaultValue="sarah.thompson@email.com"
                      className="w-full px-3 py-2 border border-vita-grey-light rounded-xl focus:ring-2 focus:ring-vita-blue focus:border-transparent transition-colors"
                    />
                  </div>
                </div>
              </div>
              
              <button className="w-full p-3 border border-vita-grey-light rounded-2xl text-sm font-medium text-gray-700 hover:bg-vita-grey-light transition-colors">
                + Add Another Emergency Contact
              </button>
            </div>
          </div>
        )}

        {activeTab === 'preferences' && (
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-6">Notification Preferences</h3>
            <div className="space-y-6">
              <div>
                <h4 className="font-medium text-gray-900 mb-3">Health Alerts</h4>
                <div className="space-y-3">
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Heart rate anomalies</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Blood pressure changes</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Temperature alerts</span>
                  </label>
                </div>
              </div>

              <div>
                <h4 className="font-medium text-gray-900 mb-3">Location Alerts</h4>
                <div className="space-y-3">
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Geofence exits</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Extended time away from home</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" />
                    <span className="ml-2 text-sm text-gray-700">Movement during sleep hours</span>
                  </label>
                </div>
              </div>

              <div>
                <h4 className="font-medium text-gray-900 mb-3">Medication Reminders</h4>
                <div className="space-y-3">
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Missed medication alerts</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Refill reminders</span>
                  </label>
                  <label className="flex items-center">
                    <input type="checkbox" className="rounded border-gray-300" defaultChecked />
                    <span className="ml-2 text-sm text-gray-700">Medication interaction warnings</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        )}

        <div className="mt-8 flex items-center justify-end space-x-4">
          <button className="px-4 py-2 text-sm font-medium text-gray-700 bg-vita-grey-light hover:bg-vita-grey rounded-2xl transition-colors">
            Cancel
          </button>
          <button className="px-4 py-2 text-sm font-medium text-white bg-vita-blue hover:bg-vita-blue-dark rounded-2xl transition-colors">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  );
};

export default ProfileSettings;