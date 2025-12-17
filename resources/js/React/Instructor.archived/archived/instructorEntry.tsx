import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import InstructorDashboard from "./Components/InstructorDashboard";

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5,
      refetchOnWindowFocus: false,
      retry: (failureCount: number, error: any) => {
        if (error?.status >= 400 && error?.status < 500) return false;
        return failureCount < 3;
      },
    },
  },
});

const InstructorAppWrapper: React.FC<{ children: React.ReactNode }> = ({ children }) => (
  <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
);

export const InstructorEntry: React.FC = () => (
    <InstructorAppWrapper>
        <InstructorDashboard />
    </InstructorAppWrapper>
);

// Mounting logic
function mountInstructor() {
  const container = document.getElementById('instructor-dashboard-container');
  if (!container) return;
  const root = createRoot(container);
  root.render(<InstructorEntry />);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountInstructor);
} else {
  mountInstructor();
}

export default InstructorEntry;
