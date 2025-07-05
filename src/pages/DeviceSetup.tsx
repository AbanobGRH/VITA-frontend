import React, { useState } from 'react';
import { QrCode, Smartphone, Wifi, Bluetooth, Battery, CheckCircle, AlertCircle } from 'lucide-react';

const DeviceSetup: React.FC = () => {
  const [setupStep, setSetupStep] = useState(1);
  const [deviceConnected, setDeviceConnected] = useState(false);

  const setupSteps = [
    { id: 1, title: 'Scan QR Code', description: 'Scan the QR code on your VITA bracelet' },
    { id: 2, title: 'Pair Device', description: 'Connect your bracelet to the platform' },
    { id: 3, title: 'Configure Settings', description: 'Set up your personal preferences' },
    { id: 4, title: 'Test Connection', description: 'Verify everything is working properly' }
  ];

  const deviceInfo = {
    model: 'VITA Smart Bracelet v2.0',
    serialNumber: 'VSB-2024-001-MT',
    firmwareVersion: '2.1.4',
    batteryLevel: 87,
    lastSync: '2 minutes ago'
  };

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Device Setup</h1>
        <p className="text-gray-600 mt-2">Configure and manage your VITA smart bracelet</p>
      </div>

      {/* Setup Progress */}
      <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6 mb-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Setup Progress</h2>
        <div className="flex items-center justify-between">
          {setupSteps.map((step, index) => (
            <div key={step.id} className="flex items-center">
              <div className={`flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors ${
                setupStep >= step.id 
                  ? 'bg-vita-blue border-vita-blue text-white' 
                  : 'border-vita-grey text-gray-400'
              }`}>
                {setupStep > step.id ? (
                  <CheckCircle className="h-5 w-5" />
                ) : (
                  <span className="text-sm font-medium">{step.id}</span>
                )}
              </div>
              {index < setupSteps.length - 1 && (
                <div className={`w-24 h-0.5 mx-4 transition-colors ${
                  setupStep > step.id ? 'bg-vita-blue' : 'bg-vita-grey'
                }`} />
              )}
            </div>
          ))}
        </div>
        <div className="mt-4 text-center">
          <p className="text-sm font-medium text-gray-900">
            {setupSteps[setupStep - 1]?.title}
          </p>
          <p className="text-xs text-gray-500 mt-1">
            {setupSteps[setupStep - 1]?.description}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* QR Code Section */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center space-x-2 mb-6">
            <QrCode className="h-5 w-5 text-vita-blue" />
            <h2 className="text-xl font-semibold text-gray-900">QR Code Scanner</h2>
          </div>
          
          <div className="text-center">
            <div className="w-48 h-48 mx-auto bg-gradient-to-br from-vita-blue from-opacity-5 to-vita-mint to-opacity-10 rounded-2xl border-2 border-dashed border-vita-grey flex items-center justify-center mb-4">
              <div className="text-center">
                <QrCode className="h-16 w-16 text-gray-400 mx-auto mb-2" />
                <p className="text-sm text-gray-600">Scan QR code on bracelet</p>
              </div>
            </div>
            
            <div className="space-y-3">
              <button className="w-full bg-vita-blue hover:bg-vita-blue-dark text-white py-3 px-4 rounded-2xl font-medium transition-colors">
                <div className="flex items-center justify-center space-x-2">
                  <Smartphone className="h-4 w-4" />
                  <span>Use Camera to Scan</span>
                </div>
              </button>
              
              <button className="w-full border border-vita-grey-light hover:bg-vita-grey-light text-gray-700 py-2 px-4 rounded-2xl font-medium transition-colors">
                Enter Serial Number Manually
              </button>
            </div>
          </div>
        </div>

        {/* Device Status */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Device Status</h2>
            <div className="flex items-center space-x-2">
              <div className={`w-2 h-2 rounded-full ${deviceConnected ? 'bg-vita-mint' : 'bg-gray-400'}`} />
              <span className={`text-sm font-medium ${deviceConnected ? 'text-vita-mint' : 'text-gray-500'}`}>
                {deviceConnected ? 'Connected' : 'Not Connected'}
              </span>
            </div>
          </div>
          
          <div className="space-y-4">
            <div className="p-4 bg-vita-white rounded-xl border border-vita-grey-light">
              <div className="flex items-center justify-between mb-2">
                <span className="text-sm font-medium text-gray-700">Model</span>
                <span className="text-sm text-gray-900">{deviceInfo.model}</span>
              </div>
              <div className="flex items-center justify-between mb-2">
                <span className="text-sm font-medium text-gray-700">Serial Number</span>
                <span className="text-sm text-gray-900 font-mono">{deviceInfo.serialNumber}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-sm font-medium text-gray-700">Firmware</span>
                <span className="text-sm text-gray-900">{deviceInfo.firmwareVersion}</span>
              </div>
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="p-3 bg-vita-mint bg-opacity-10 rounded-xl border border-vita-mint border-opacity-30">
                <div className="flex items-center space-x-2">
                  <Battery className="h-4 w-4 text-vita-mint" />
                  <span className="text-sm font-medium text-vita-mint-dark">Battery</span>
                </div>
                <p className="text-lg font-bold text-vita-mint-dark mt-1">{deviceInfo.batteryLevel}%</p>
              </div>
              
              <div className="p-3 bg-vita-blue bg-opacity-10 rounded-xl border border-vita-blue border-opacity-30">
                <div className="flex items-center space-x-2">
                  <Wifi className="h-4 w-4 text-vita-blue" />
                  <span className="text-sm font-medium text-vita-blue-dark">Sync</span>
                </div>
                <p className="text-xs text-vita-blue mt-1">{deviceInfo.lastSync}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Connection Settings */}
      <div className="mt-8 bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Connection Settings</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <div className="flex items-center justify-between mb-3">
              <div className="flex items-center space-x-2">
                <Wifi className="h-5 w-5 text-vita-blue" />
                <span className="font-medium text-gray-900">Wi-Fi Connection</span>
              </div>
              <CheckCircle className="h-5 w-5 text-vita-mint" />
            </div>
            <p className="text-sm text-gray-600 mb-3">Connected to: Home_Network_5G</p>
            <button className="text-sm text-vita-blue hover:text-vita-blue-dark transition-colors">Change Network</button>
          </div>
          
          <div className="p-4 border border-vita-grey-light rounded-2xl">
            <div className="flex items-center justify-between mb-3">
              <div className="flex items-center space-x-2">
                <Bluetooth className="h-5 w-5 text-vita-blue" />
                <span className="font-medium text-gray-900">Bluetooth Pairing</span>
              </div>
              <CheckCircle className="h-5 w-5 text-vita-mint" />
            </div>
            <p className="text-sm text-gray-600 mb-3">Paired with smartphone</p>
            <button className="text-sm text-vita-blue hover:text-vita-blue-dark transition-colors">Manage Pairing</button>
          </div>
        </div>
      </div>

      {/* Troubleshooting */}
      <div className="mt-8 bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Troubleshooting</h2>
        
        <div className="space-y-4">
          <div className="p-4 bg-orange-50 border border-orange-200 rounded-2xl">
            <div className="flex items-start space-x-3">
              <AlertCircle className="h-5 w-5 text-orange-600 mt-0.5" />
              <div>
                <h3 className="font-medium text-orange-800">Device Not Responding?</h3>
                <p className="text-sm text-orange-700 mt-1">Try restarting the bracelet by holding the side button for 10 seconds.</p>
                <button className="text-sm text-orange-600 hover:text-orange-700 font-medium mt-2 transition-colors">
                  View Troubleshooting Guide
                </button>
              </div>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button className="p-4 text-left border border-vita-grey-light rounded-2xl hover:bg-vita-grey-light transition-colors">
              <h3 className="font-medium text-gray-900">Reset Device</h3>
              <p className="text-sm text-gray-600 mt-1">Factory reset your VITA bracelet</p>
            </button>
            
            <button className="p-4 text-left border border-vita-grey-light rounded-2xl hover:bg-vita-grey-light transition-colors">
              <h3 className="font-medium text-gray-900">Contact Support</h3>
              <p className="text-sm text-gray-600 mt-1">Get help from our technical team</p>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DeviceSetup;