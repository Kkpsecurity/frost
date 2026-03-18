import React from "react";
import { t } from "@/i18n";

interface SelfStudyHelpModalProps {
    isOpen: boolean;
    onClose: () => void;
}

const overlayStyle: React.CSSProperties = {
    position: "fixed",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    background: "rgba(0, 0, 0, 0.75)",
    zIndex: 9000,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    padding: "1rem",
};

const modalStyle: React.CSSProperties = {
    background: "#1e2d3d",
    border: "1px solid #34495e",
    borderRadius: "0.75rem",
    width: "100%",
    maxWidth: "640px",
    maxHeight: "85vh",
    display: "flex",
    flexDirection: "column",
    boxShadow: "0 20px 60px rgba(0,0,0,0.6)",
};

const headerStyle: React.CSSProperties = {
    display: "flex",
    alignItems: "center",
    justifyContent: "space-between",
    padding: "1.25rem 1.5rem 1rem",
    borderBottom: "1px solid #34495e",
    flexShrink: 0,
};

const bodyStyle: React.CSSProperties = {
    overflowY: "auto",
    padding: "1.25rem 1.5rem 1.5rem",
};

const sectionStyle: React.CSSProperties = {
    marginBottom: "1.5rem",
};

const sectionTitleStyle: React.CSSProperties = {
    color: "#3498db",
    fontWeight: 700,
    fontSize: "0.95rem",
    marginBottom: "0.5rem",
    display: "flex",
    alignItems: "center",
    gap: "0.5rem",
};

const bodyTextStyle: React.CSSProperties = {
    color: "#bdc3c7",
    fontSize: "0.875rem",
    lineHeight: 1.6,
};

const listStyle: React.CSSProperties = {
    color: "#bdc3c7",
    fontSize: "0.875rem",
    lineHeight: 1.8,
    paddingLeft: "1.25rem",
    margin: 0,
};

const dividerStyle: React.CSSProperties = {
    borderColor: "#2c3e50",
    margin: "0 0 1.5rem",
};

const SelfStudyHelpModal: React.FC<SelfStudyHelpModalProps> = ({
    isOpen,
    onClose,
}) => {
    if (!isOpen) return null;

    const handleOverlayClick = (e: React.MouseEvent<HTMLDivElement>) => {
        if (e.target === e.currentTarget) onClose();
    };

    return (
        <div style={overlayStyle} onClick={handleOverlayClick}>
            <div style={modalStyle} role="dialog" aria-modal="true" aria-labelledby="help-modal-title">

                {/* Header */}
                <div style={headerStyle}>
                    <h5
                        id="help-modal-title"
                        style={{ color: "white", margin: 0, fontWeight: 700 }}
                    >
                        <i className="fas fa-question-circle me-2" style={{ color: "#3498db" }}></i>
                        {t("offlineTab.helpTitle")}
                    </h5>
                    <button
                        onClick={onClose}
                        style={{
                            background: "none",
                            border: "none",
                            color: "#95a5a6",
                            fontSize: "1.25rem",
                            cursor: "pointer",
                            lineHeight: 1,
                            padding: "0.25rem",
                        }}
                        aria-label={t("offlineTab.closeHelp")}
                    >
                        ×
                    </button>
                </div>

                {/* Body */}
                <div style={bodyStyle}>

                    {/* What is Self Study */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-graduation-cap"></i>
                            {t("offlineTab.whatIsSelfStudy")}
                        </div>
                        <p style={bodyTextStyle}>
                            {t("offlineTab.selfStudyDesc")}
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Video Quota */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-hourglass-half" style={{ color: "#f39c12" }}></i>
                            {t("offlineTab.helpVideoQuotaTitle")}
                        </div>
                        <p style={{ ...bodyTextStyle, marginBottom: "0.5rem" }}>
                            {t("offlineTab.videoQuotaDesc")}
                        </p>
                        <ul style={listStyle}>
                            <li><strong style={{ color: "#ecf0f1" }}>{t("offlineTab.quotaTotal")}</strong> — {t("offlineTab.quotaTotalDesc")}</li>
                            <li><strong style={{ color: "#ecf0f1" }}>{t("offlineTab.quotaUsed")}</strong> — {t("offlineTab.quotaUsedDesc")}</li>
                            <li><strong style={{ color: "#ecf0f1" }}>{t("offlineTab.quotaRemaining")}</strong> — {t("offlineTab.quotaRemainingDesc")}</li>
                            <li><strong style={{ color: "#ecf0f1" }}>{t("offlineTab.quotaRefunded")}</strong> — {t("offlineTab.quotaRefundedDesc")}</li>
                        </ul>
                        <p style={{ ...bodyTextStyle, marginTop: "0.75rem" }}>
                            {t("offlineTab.quotaManage")}
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* How Sessions Work */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-film" style={{ color: "#3498db" }}></i>
                            {t("offlineTab.howSessionsWork")}
                        </div>
                        <ol style={{ ...listStyle, listStyleType: "decimal" }}>
                            <li>{t("offlineTab.sessionStep1")}</li>
                            <li>{t("offlineTab.sessionStep2")}</li>
                            <li>{t("offlineTab.sessionStep3")}</li>
                            <li>{t("offlineTab.sessionStep4")}</li>
                            <li>{t("offlineTab.sessionStep5")}</li>
                        </ol>
                        <p style={{ ...bodyTextStyle, marginTop: "0.75rem" }}>
                            <i className="fas fa-info-circle me-1" style={{ color: "#3498db" }}></i>
                            {t("offlineTab.sessionProgressNote")}
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Breaks */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-coffee" style={{ color: "#e67e22" }}></i>
                            {t("offlineTab.breaksTitle")}
                        </div>
                        <p style={{ ...bodyTextStyle, marginBottom: "0.5rem" }}>
                            {t("offlineTab.breaksDesc")}
                        </p>
                        <ul style={listStyle}>
                            <li>{t("offlineTab.breaksItem1")}</li>
                            <li>{t("offlineTab.breaksItem2")}</li>
                            <li>{t("offlineTab.breaksItem3")}</li>
                        </ul>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Completion */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-check-circle" style={{ color: "#2ecc71" }}></i>
                            {t("offlineTab.completingTitle")}
                        </div>
                        <ul style={listStyle}>
                            <li>{t("offlineTab.completingItem1")}</li>
                            <li>{t("offlineTab.completingItem2")}</li>
                            <li>{t("offlineTab.completingItem3")}</li>
                            <li>{t("offlineTab.completingItem4")}</li>
                        </ul>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Exam Eligibility */}
                    <div style={{ ...sectionStyle, marginBottom: 0 }}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-file-alt" style={{ color: "#9b59b6" }}></i>
                            {t("offlineTab.examEligibilityTitle")}
                        </div>
                        <ul style={listStyle}>
                            <li>{t("offlineTab.examEligItem1")}</li>
                            <li>{t("offlineTab.examEligItem2")}</li>
                            <li>{t("offlineTab.examEligItem3")}</li>
                            <li>{t("offlineTab.examEligItem4")}</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    );
};

export default SelfStudyHelpModal;
