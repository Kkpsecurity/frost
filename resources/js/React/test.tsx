import * as React from 'react';
import * as ReactDOM from 'react-dom/client';

const TestComponent = () => {
    return React.createElement(
        'div',
        null,
        React.createElement('h1', null, 'Test React Component'),
        React.createElement('p', null, 'If you can see this, React is working!')
    );
};

// Auto-mount when this module loads
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("instructor-dashboard-container");
    if (container) {
        const root = ReactDOM.createRoot(container);
        root.render(React.createElement(TestComponent));
        console.log("Test React component mounted");
    }
});

export default TestComponent;
