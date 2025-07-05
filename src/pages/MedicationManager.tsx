import React, { useState } from 'react';
import { Pill, Clock, AlertTriangle, Plus, Calendar, Check } from 'lucide-react';

const MedicationManager: React.FC = () => {
  const [activeTab, setActiveTab] = useState('current');

  const medications = [
    {
      id: 1,
      name: 'Lisinopril',
      dosage: '10mg',
      frequency: 'Once daily',
      nextDose: '8:00 AM',
      remaining: 28,
      purpose: 'Blood pressure',
      status: 'active'
    },
    {
      id: 2,
      name: 'Metformin',
      dosage: '500mg',
      frequency: 'Twice daily',
      nextDose: '6:00 PM',
      remaining: 45,
      purpose: 'Diabetes',
      status: 'active'
    },
    {
      id: 3,
      name: 'Vitamin D3',
      dosage: '1000 IU',
      frequency: 'Once daily',
      nextDose: '8:00 AM',
      remaining: 60,
      purpose: 'Bone health',
      status: 'active'
    }
  ];

  const todaySchedule = [
    { time: '6:00 AM', medication: 'Metformin 500mg', status: 'taken' },
    { time: '8:00 AM', medication: 'Lisinopril 10mg', status: 'taken' },
    { time: '8:00 AM', medication: 'Vitamin D3 1000 IU', status: 'taken' },
    { time: '6:00 PM', medication: 'Metformin 500mg', status: 'pending' },
  ];

  const upcomingReminders = [
    { time: '6:00 PM', medication: 'Metformin', dosage: '500mg', minutes: 45 },
    { time: '8:00 PM', medication: 'Calcium', dosage: '600mg', minutes: 165 },
  ];

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Medication Manager</h1>
        <p className="text-gray-600 mt-2">Track medications, schedules, and adherence</p>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Active Medications</p>
              <p className="text-2xl font-bold text-gray-900 mt-1">3</p>
            </div>
            <div className="p-3 rounded-2xl bg-vita-blue bg-opacity-10">
              <Pill className="h-8 w-8 text-vita-blue" />
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Today's Adherence</p>
              <p className="text-2xl font-bold text-vita-mint mt-1">75%</p>
            </div>
            <div className="p-3 rounded-2xl bg-vita-mint bg-opacity-10">
              <Check className="h-8 w-8 text-vita-mint" />
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Next Dose</p>
              <p className="text-2xl font-bold text-orange-600 mt-1">45m</p>
            </div>
            <div className="p-3 rounded-2xl bg-orange-50">
              <Clock className="h-8 w-8 text-orange-600" />
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Refills Needed</p>
              <p className="text-2xl font-bold text-vita-coral mt-1">1</p>
            </div>
            <div className="p-3 rounded-2xl bg-vita-coral bg-opacity-10">
              <AlertTriangle className="h-8 w-8 text-vita-coral" />
            </div>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="mb-8">
        <nav className="flex space-x-8">
          {['current', 'schedule', 'history'].map((tab) => (
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
      {activeTab === 'current' && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Current Medications */}
          <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-xl font-semibold text-gray-900">Current Medications</h2>
              <button className="flex items-center space-x-2 text-vita-blue hover:text-vita-blue-dark transition-colors">
                <Plus className="h-4 w-4" />
                <span className="text-sm font-medium">Add Medication</span>
              </button>
            </div>
            
            <div className="space-y-4">
              {medications.map((med) => (
                <div key={med.id} className="p-4 border border-vita-grey-light rounded-2xl">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <h3 className="font-medium text-gray-900">{med.name}</h3>
                      <p className="text-sm text-gray-600">{med.dosage} • {med.frequency}</p>
                      <p className="text-xs text-gray-500 mt-1">{med.purpose}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-medium text-gray-900">Next: {med.nextDose}</p>
                      <p className={`text-xs mt-1 ${
                        med.remaining <= 7 ? 'text-vita-coral' : 'text-gray-500'
                      }`}>
                        {med.remaining} pills left
                      </p>
                    </div>
                  </div>
                  <div className="mt-3 flex items-center space-x-4">
                    <button className="text-xs text-vita-blue hover:text-vita-blue-dark transition-colors">Edit</button>
                    <button className="text-xs text-vita-mint hover:text-vita-mint-dark transition-colors">Mark Taken</button>
                    <button className="text-xs text-vita-coral hover:text-red-700 transition-colors">Remove</button>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Upcoming Reminders */}
          <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-xl font-semibold text-gray-900">Upcoming Reminders</h2>
              <Clock className="h-5 w-5 text-gray-400" />
            </div>
            
            <div className="space-y-4">
              {upcomingReminders.map((reminder, index) => (
                <div key={index} className="p-4 bg-vita-blue bg-opacity-10 border border-vita-blue border-opacity-30 rounded-2xl">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="font-medium text-vita-blue-dark">{reminder.medication}</p>
                      <p className="text-sm text-vita-blue">{reminder.dosage} at {reminder.time}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-medium text-vita-blue-dark">
                        in {reminder.minutes} min
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {/* Refill Alerts */}
            <div className="mt-6 p-4 bg-vita-coral bg-opacity-10 border border-vita-coral border-opacity-30 rounded-2xl">
              <div className="flex items-center space-x-2">
                <AlertTriangle className="h-4 w-4 text-vita-coral" />
                <span className="text-sm font-medium text-vita-coral">Refill Alert</span>
              </div>
              <p className="text-sm text-vita-coral mt-1">
                Lisinopril is running low (7 days remaining)
              </p>
              <button className="mt-2 text-sm text-vita-coral hover:text-red-700 font-medium transition-colors">
                Order Refill
              </button>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'schedule' && (
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Today's Schedule</h2>
            <Calendar className="h-5 w-5 text-gray-400" />
          </div>
          
          <div className="space-y-4">
            {todaySchedule.map((item, index) => (
              <div key={index} className={`p-4 rounded-2xl border ${
                item.status === 'taken' 
                  ? 'bg-vita-mint bg-opacity-10 border-vita-mint border-opacity-30' 
                  : 'bg-orange-50 border-orange-200'
              }`}>
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className={`w-3 h-3 rounded-full ${
                      item.status === 'taken' ? 'bg-vita-mint' : 'bg-orange-500'
                    }`} />
                    <div>
                      <p className="font-medium text-gray-900">{item.medication}</p>
                      <p className="text-sm text-gray-600">{item.time}</p>
                    </div>
                  </div>
                  <span className={`text-xs font-medium px-2 py-1 rounded-full ${
                    item.status === 'taken' 
                      ? 'bg-vita-mint bg-opacity-20 text-vita-mint-dark' 
                      : 'bg-orange-100 text-orange-800'
                  }`}>
                    {item.status === 'taken' ? 'Taken' : 'Pending'}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {activeTab === 'history' && (
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Medication History</h2>
          <div className="text-center py-12">
            <Calendar className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-600">Medication history and adherence reports</p>
            <p className="text-sm text-gray-500 mt-2">Track long-term medication patterns</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default MedicationManager;