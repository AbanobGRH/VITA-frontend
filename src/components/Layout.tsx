import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { 
  Heart, 
  MapPin, 
  Pill, 
  AlertTriangle, 
  User, 
  Settings, 
  Menu, 
  X,
  Shield,
  Activity
} from 'lucide-react';

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const location = useLocation();

  const navigation = [
    { name: 'Dashboard', href: '/', icon: Activity },
    { name: 'Health Metrics', href: '/health', icon: Heart },
    { name: 'Location', href: '/location', icon: MapPin },
    { name: 'Medication', href: '/medication', icon: Pill },
    { name: 'Alerts', href: '/alerts', icon: AlertTriangle },
    { name: 'Profile', href: '/profile', icon: User },
    { name: 'Device Setup', href: '/setup', icon: Settings },
  ];

  return (
    <div className="min-h-screen bg-vita-white">
      {/* Mobile sidebar */}
      <div className={`fixed inset-0 z-50 lg:hidden ${sidebarOpen ? 'block' : 'hidden'}`}>
        <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)} />
        <div className="fixed inset-y-0 left-0 w-64 bg-white shadow-soft-lg">
          <div className="flex items-center justify-between h-16 px-4 border-b border-vita-grey-light">
            <div className="flex items-center space-x-2">
              <Shield className="h-8 w-8 text-vita-blue" />
              <span className="text-xl font-bold text-gray-900">VITA</span>
            </div>
            <button onClick={() => setSidebarOpen(false)} className="text-gray-400 hover:text-gray-600 transition-colors">
              <X className="h-6 w-6" />
            </button>
          </div>
          <nav className="mt-8">
            {navigation.map((item) => {
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center px-4 py-3 text-sm font-medium transition-colors ${
                    location.pathname === item.href
                      ? 'bg-vita-blue bg-opacity-10 text-vita-blue border-r-2 border-vita-blue'
                      : 'text-gray-600 hover:bg-vita-grey-light hover:text-gray-900'
                  }`}
                  onClick={() => setSidebarOpen(false)}
                >
                  <Icon className="mr-3 h-5 w-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      </div>

      {/* Desktop sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex flex-col flex-grow bg-white border-r border-vita-grey-light shadow-soft">
          <div className="flex items-center h-16 px-4 border-b border-vita-grey-light">
            <Shield className="h-8 w-8 text-vita-blue" />
            <span className="ml-2 text-xl font-bold text-gray-900">VITA</span>
          </div>
          <nav className="mt-8 flex-1">
            {navigation.map((item) => {
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center px-4 py-3 text-sm font-medium transition-colors ${
                    location.pathname === item.href
                      ? 'bg-vita-blue bg-opacity-10 text-vita-blue border-r-2 border-vita-blue'
                      : 'text-gray-600 hover:bg-vita-grey-light hover:text-gray-900'
                  }`}
                >
                  <Icon className="mr-3 h-5 w-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      </div>

      {/* Main content */}
      <div className="lg:pl-64">
        {/* Mobile header */}
        <div className="lg:hidden flex items-center justify-between h-16 px-4 bg-white border-b border-vita-grey-light">
          <button
            onClick={() => setSidebarOpen(true)}
            className="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <Menu className="h-6 w-6" />
          </button>
          <div className="flex items-center space-x-2">
            <Shield className="h-6 w-6 text-vita-blue" />
            <span className="text-lg font-bold text-gray-900">VITA</span>
          </div>
          <div className="w-6" />
        </div>

        {/* Page content */}
        <main className="flex-1">
          {children}
        </main>
      </div>
    </div>
  );
};

export default Layout;