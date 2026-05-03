import React from 'react';
import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import Navbar from './components/Navbar';
import AdminNavbar from './components/AdminNavbar';
import Footer from './components/Footer';
import ProjectList from './pages/ProjectList';
import ProjectDetail from './pages/ProjectDetail';
import ProviderList from './pages/ProviderList';

function Layout({ children }) {
  const location = useLocation();
  const isAdminRoute = location.pathname.startsWith('/providers') || location.pathname.startsWith('/admin');

  return (
    <div className="min-h-screen flex flex-col font-body text-gray-900 bg-[#f7faf9]">
      {isAdminRoute ? <AdminNavbar /> : <Navbar />}
      {children}
      <Footer />
    </div>
  );
}

function App() {
  return (
    <BrowserRouter>
      <Layout>
        <Routes>
          <Route path="/" element={<ProjectList />} />
          <Route path="/project/:id" element={<ProjectDetail />} />
          <Route path="/providers" element={<ProviderList />} />
        </Routes>
      </Layout>
    </BrowserRouter>
  );
}

export default App;
