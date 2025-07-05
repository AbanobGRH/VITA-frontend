import React, { useState } from 'react';
import { Heart, Activity, Thermometer, Droplets, Calendar, TrendingUp } from 'lucide-react';

const HealthMetrics: React.FC = () => {
  const [selectedPeriod, setSelectedPeriod] = useState('week');

  const metrics = [
    {
      name: 'Heart Rate',
      current: '72 bpm',
      average: '75 bpm',
      trend: 'stable',
      color: 'text-vita-coral',
      bgColor: 'bg-vita-coral bg-opacity-10',
      icon: Heart
    },
    {
      name: 'Blood Pressure',
      current: '120/80 mmHg',
      average: '125/82 mmHg',
      trend: 'improving',
      color: 'text-vita-blue',
      bgColor: 'bg-vita-blue bg-opacity-10',
      icon: Activity
    },
    {
      name: 'Body Temperature',
      current: '98.6°F',
      average: '98.4°F',
      trend: 'stable',
      color: 'text-orange-500',
      bgColor: 'bg-orange-50',
      icon: Thermometer
    },
    {
      name: 'Blood Oxygen',
      current: '98%',
      average: '97%',
      trend: 'improving',
      color: 'text-vita-mint',
      bgColor: 'bg-vita-mint bg-opacity-10',
      icon: Droplets
    }
  ];

  const recentReadings = [
    { time: '2:30 PM', heartRate: 68, bloodPressure: '118/78', temp: '98.4°F', oxygen: '98%' },
    { time: '12:00 PM', heartRate: 72, bloodPressure: '120/80', temp: '98.6°F', oxygen: '97%' },
    { time: '9:30 AM', heartRate: 78, bloodPressure: '125/82', temp: '98.3°F', oxygen: '98%' },
    { time: '6:15 AM', heartRate: 65, bloodPressure: '115/75', temp: '98.2°F', oxygen: '97%' },
  ];

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Health Metrics</h1>
        <p className="text-gray-600 mt-2">Comprehensive health monitoring and trends</p>
      </div>

      {/* Time Period Selector */}
      <div className="mb-8">
        <div className="flex space-x-1 bg-vita-grey-light p-1 rounded-2xl w-fit">
          {['day', 'week', 'month', 'year'].map((period) => (
            <button
              key={period}
              onClick={() => setSelectedPeriod(period)}
              className={`px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 ${
                selectedPeriod === period
                  ? 'bg-white text-gray-900 shadow-soft'
                  : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              {period.charAt(0).toUpperCase() + period.slice(1)}
            </button>
          ))}
        </div>
      </div>

      {/* Metrics Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {metrics.map((metric) => {
          const Icon = metric.icon;
          return (
            <div key={metric.name} className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6 hover:shadow-soft-lg transition-all duration-300">
              <div className="flex items-center justify-between mb-4">
                <div className={`p-3 rounded-2xl ${metric.bgColor}`}>
                  <Icon className={`h-6 w-6 ${metric.color}`} />
                </div>
                <div className="flex items-center space-x-1">
                  <TrendingUp className={`h-4 w-4 ${
                    metric.trend === 'improving' ? 'text-vita-mint' : 'text-gray-400'
                  }`} />
                  <span className={`text-xs font-medium ${
                    metric.trend === 'improving' ? 'text-vita-mint' : 'text-gray-500'
                  }`}>
                    {metric.trend}
                  </span>
                </div>
              </div>
              <div>
                <h3 className="text-sm font-medium text-gray-600">{metric.name}</h3>
                <p className="text-2xl font-bold text-gray-900 mt-1">{metric.current}</p>
                <p className="text-sm text-gray-500 mt-1">Avg: {metric.average}</p>
              </div>
            </div>
          );
        })}
      </div>

      {/* Charts and Detailed View */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Heart Rate Chart Placeholder */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Heart Rate Trends</h2>
            <Heart className="h-5 w-5 text-vita-coral" />
          </div>
          <div className="h-64 bg-gradient-to-br from-vita-coral from-opacity-5 to-vita-coral to-opacity-10 rounded-2xl flex items-center justify-center border border-vita-grey-light">
            <div className="text-center">
              <Activity className="h-12 w-12 text-vita-coral mx-auto mb-2" />
              <p className="text-sm text-gray-600">Interactive heart rate chart</p>
              <p className="text-xs text-gray-500 mt-1">Real-time data visualization</p>
            </div>
          </div>
          <div className="mt-4 flex items-center justify-between text-sm">
            <span className="text-gray-600">Resting HR: 65 bpm</span>
            <span className="text-gray-600">Max HR: 142 bpm</span>
          </div>
        </div>

        {/* Recent Readings */}
        <div className="bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-semibold text-gray-900">Recent Readings</h2>
            <Calendar className="h-5 w-5 text-gray-400" />
          </div>
          <div className="space-y-4">
            {recentReadings.map((reading, index) => (
              <div key={index} className="p-4 bg-vita-white rounded-xl border border-vita-grey-light">
                <div className="flex items-center justify-between mb-2">
                  <span className="text-sm font-medium text-gray-900">{reading.time}</span>
                  <span className="text-xs text-gray-500">Today</span>
                </div>
                <div className="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span className="text-gray-600">HR:</span>
                    <span className="ml-1 font-medium">{reading.heartRate} bpm</span>
                  </div>
                  <div>
                    <span className="text-gray-600">BP:</span>
                    <span className="ml-1 font-medium">{reading.bloodPressure}</span>
                  </div>
                  <div>
                    <span className="text-gray-600">Temp:</span>
                    <span className="ml-1 font-medium">{reading.temp}</span>
                  </div>
                  <div>
                    <span className="text-gray-600">O2:</span>
                    <span className="ml-1 font-medium">{reading.oxygen}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Health Insights */}
      <div className="mt-8 bg-white rounded-2xl shadow-soft border border-vita-grey-light p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-6">AI Health Insights</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="p-4 bg-vita-mint bg-opacity-10 border border-vita-mint border-opacity-30 rounded-2xl">
            <h3 className="text-sm font-medium text-vita-mint-dark mb-2">Positive Trends</h3>
            <ul className="text-sm text-vita-mint-dark space-y-1">
              <li>• Blood pressure has improved by 5% this week</li>
              <li>• Heart rate variability is within optimal range</li>
              <li>• Sleep quality has been consistently good</li>
            </ul>
          </div>
          <div className="p-4 bg-vita-blue bg-opacity-10 border border-vita-blue border-opacity-30 rounded-2xl">
            <h3 className="text-sm font-medium text-vita-blue-dark mb-2">Recommendations</h3>
            <ul className="text-sm text-vita-blue-dark space-y-1">
              <li>• Continue current medication schedule</li>
              <li>• Maintain regular walking routine</li>
              <li>• Consider increasing water intake</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HealthMetrics;