<style>
/* ===================================
   Enhanced Media Manager Styles
   =================================== */

/* Reset and Base Styles */
.media-manager-container * {
    box-sizing: border-box;
}

/* Main Container */
.media-manager-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 20px !important;
    padding: 0 !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    overflow: hidden !important;
    margin: 1rem 0 !important;
}

/* Tab Navigation */
.media-manager-container .media-tabs {
    background: rgba(255, 255, 255, 0.1) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 20px 20px 0 0 !important;
    padding: 0.5rem 1rem !important;
}

.media-manager-container .media-tabs .nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
    background: transparent !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
    margin: 0 0.25rem !important;
    transition: all 0.3s ease !important;
    font-weight: 500 !important;
    position: relative !important;
}

.media-manager-container .media-tabs .nav-link:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.1) !important;
    transform: translateY(-2px) !important;
    text-decoration: none !important;
}

.media-manager-container .media-tabs .nav-link.active {
    color: white !important;
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.media-manager-container .media-tabs .nav-link i {
    margin-right: 0.5rem !important;
}

/* Tab Content */
.media-manager-container .tab-content {
    background: rgba(255, 255, 255, 0.95) !important;
    border-radius: 0 0 20px 20px !important;
    min-height: 600px !important;
    position: relative !important;
}

.media-manager-container .tab-pane {
    padding: 2rem !important;
    min-height: 600px !important;
}

/* Upload Area */
.media-manager-container .upload-area {
    border: 3px dashed rgba(0, 123, 255, 0.3) !important;
    border-radius: 20px !important;
    padding: 3rem 2rem !important;
    text-align: center !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(243, 247, 255, 0.9) 100%) !important;
    transition: all 0.4s ease !important;
    cursor: pointer !important;
    position: relative !important;
    overflow: hidden !important;
    margin-bottom: 2rem !important;
}

.media-manager-container .upload-area:hover {
    border-color: #007bff !important;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.05) 0%, rgba(0, 123, 255, 0.1) 100%) !important;
    transform: translateY(-4px) !important;
    box-shadow: 0 15px 35px rgba(0, 123, 255, 0.2) !important;
}

.media-manager-container .upload-area i {
    color: #6c757d !important;
    margin-bottom: 1.5rem !important;
    transition: all 0.4s ease !important;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1)) !important;
}

.media-manager-container .upload-area:hover i {
    color: #007bff !important;
    transform: scale(1.15) translateY(-5px) !important;
}

.media-manager-container .upload-area h4 {
    color: #2c3e50 !important;
    font-weight: 700 !important;
    margin-bottom: 0.75rem !important;
    font-size: 1.5rem !important;
}

.media-manager-container .upload-area p {
    color: #6c757d !important;
    margin-bottom: 2rem !important;
    font-size: 1rem !important;
    line-height: 1.5 !important;
}

/* Upload Button */
.media-manager-container .upload-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    border: none !important;
    border-radius: 50px !important;
    padding: 1rem 2.5rem !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    transition: all 0.4s ease !important;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3) !important;
    position: relative !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.media-manager-container .upload-btn:hover {
    transform: translateY(-3px) scale(1.05) !important;
    box-shadow: 0 15px 35px rgba(0, 123, 255, 0.4) !important;
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
    color: white !important;
}

/* Media Grid */
.media-manager-container .media-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
    gap: 2rem !important;
    padding: 2rem 0 !important;
}

/* Empty State */
.media-manager-container .empty-state {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 450px !important;
    text-align: center !important;
    color: #6c757d !important;
    padding: 3rem 2rem !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(248, 250, 252, 0.8) 100%) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    position: relative !important;
    overflow: hidden !important;
}

.media-manager-container .empty-state i {
    font-size: 5rem !important;
    color: rgba(0, 123, 255, 0.2) !important;
    margin-bottom: 2rem !important;
    animation: float 3s ease-in-out infinite !important;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1)) !important;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.media-manager-container .empty-state h4 {
    color: #2c3e50 !important;
    font-weight: 700 !important;
    margin-bottom: 1rem !important;
    font-size: 1.75rem !important;
}

.media-manager-container .empty-state p {
    color: #6c757d !important;
    margin-bottom: 2.5rem !important;
    max-width: 450px !important;
    font-size: 1.1rem !important;
    line-height: 1.6 !important;
}

/* File Items */
.media-manager-container .media-item {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95)) !important;
    border-radius: 20px !important;
    padding: 1.5rem !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.4s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    position: relative !important;
    overflow: hidden !important;
}

.media-manager-container .media-item:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 20px 40px rgba(0, 123, 255, 0.15) !important;
    background: linear-gradient(145deg, rgba(255, 255, 255, 1), rgba(240, 248, 255, 1)) !important;
}

.media-manager-container .media-item .file-icon {
    text-align: center !important;
    margin-bottom: 1.5rem !important;
    position: relative !important;
}

.media-manager-container .media-item .file-icon i {
    font-size: 3rem !important;
    color: #007bff !important;
    transition: all 0.3s ease !important;
    filter: drop-shadow(0 4px 8px rgba(0, 123, 255, 0.2)) !important;
}

.media-manager-container .media-item:hover .file-icon i {
    transform: scale(1.1) !important;
    color: #0056b3 !important;
}

.media-manager-container .media-item .file-name {
    font-weight: 700 !important;
    color: #2c3e50 !important;
    margin-bottom: 0.75rem !important;
    font-size: 1rem !important;
    line-height: 1.3 !important;
    text-align: center !important;
    word-break: break-word !important;
}

.media-manager-container .media-item .file-meta {
    font-size: 0.85rem !important;
    color: #6c757d !important;
    margin-bottom: 1.5rem !important;
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0.25rem !important;
}

.media-manager-container .media-item .file-actions {
    display: flex !important;
    gap: 0.75rem !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: all 0.3s ease !important;
    transform: translateY(10px) !important;
}

.media-manager-container .media-item:hover .file-actions {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

.media-manager-container .file-action-btn {
    padding: 0.75rem !important;
    border: none !important;
    border-radius: 12px !important;
    background: rgba(248, 249, 250, 0.8) !important;
    color: #6c757d !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    font-size: 0.9rem !important;
}

.media-manager-container .file-action-btn:hover {
    background: #007bff !important;
    color: white !important;
    transform: translateY(-2px) scale(1.1) !important;
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3) !important;
}

.media-manager-container .file-action-btn.delete:hover {
    background: #dc3545 !important;
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3) !important;
}

/* Loading States */
.media-manager-container .loading-indicator {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 450px !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.media-manager-container .loading-spinner {
    text-align: center !important;
    padding: 2rem !important;
}

.media-manager-container .loading-spinner i {
    color: #007bff !important;
    margin-bottom: 1.5rem !important;
    animation: pulse 2s ease-in-out infinite !important;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .media-manager-container .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)) !important;
        gap: 1rem !important;
    }

    .media-manager-container .tab-pane {
        padding: 1rem !important;
    }

    .media-manager-container .upload-area {
        padding: 2rem 1rem !important;
    }
}
</style>
