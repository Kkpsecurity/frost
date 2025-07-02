import React, { useEffect, useState } from "react";
import { Row, Col, Container, ListGroup, ListGroupItem } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
    faUser,
    faEdit,
    faKey,
    faImage,
    faMoneyBill,
} from "@fortawesome/free-solid-svg-icons";
import EditProfileForm from "./Forms/EditProfileForm";
import EditPasswordForm from "./Forms/EditPasswordForm";
import EditAvatarForm from "./Forms/EditAvatarForm";
import BillingDetails from "./components/BillingDetails";
import Dashboard from "./components/Dashboard";
import Maintenance from "../../Maintenance";

import { LaravelDataShape } from "../../../Config/types";
import { getProfileUser } from "../../../Hooks/Web/useProfileHooks";
import PageLoader from "../../../Components/Widgets/PageLoader";

interface AccountDashboardDataLayerProps {
    debug?: boolean;
}

const AccountDashboardDataLayer: React.FC<AccountDashboardDataLayerProps> = ({
    debug = false,
}) => {

    const [laravelData, setLaravelData] = useState(null);

    const [view, setView] = useState<string>("profile");
    const { data: laravel, isLoading, error } = getProfileUser();

    const views: { [key: string]: JSX.Element } = {
        profile: <Dashboard profile={laravel?.user} />,
        "edit-profile": <EditProfileForm profile={laravel?.user} />,
        "change-password": <EditPasswordForm user_id={laravel?.user?.id} />,
        "change-avatar": (
            <EditAvatarForm avatar={laravel?.user?.avatar} debug={debug} />
        ),
        "view-billing": <BillingDetails profile={laravel} />,
        default: <Dashboard profile={laravel?.user} />,
    };

    const loadView = (selectedView: string | null) => {
        return views[selectedView!] || views["profile"];
    };

    const menuItems = [
        { name: "Dashboard", view: "profile", icon: faUser },
        { name: "Edit Profile", view: "edit-profile", icon: faEdit },
        { name: "Change Password", view: "change-password", icon: faKey },
        { name: "Change Avatar", view: "change-avatar", icon: faImage },
        { name: "Billing", view: "view-billing", icon: faMoneyBill },
    ];

    useEffect(() => {
        if (laravel) {
            setLaravelData(laravel);
        }
    }, [laravel]);

    if (isLoading) {
        return <PageLoader base_url={''} />;
    }

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    return (
        <Container className="bg-light" style={{ minHeight: "750px" }}>
            <Row>
                <Col lg={12} className="p-3">
                    <div>
                        <h3>Account Profile</h3>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col lg={3}>
                    <div className="bg-dark mb-2">
                        <ListGroup className="bg-dark">
                            {menuItems.map((item) => (
                                <ListGroupItem
                                    key={item.view}
                                    className="bg-dark"
                                    style={{
                                        color: "white",
                                    }}
                                >
                                    <button
                                        type="button"
                                        className="text-white"
                                        onClick={() => setView(item.view)}
                                    >
                                        <FontAwesomeIcon
                                            icon={item.icon}
                                            style={{ marginRight: "10px" }}
                                        />
                                        {item.name}
                                    </button>
                                </ListGroupItem>
                            ))}
                        </ListGroup>
                    </div>
                </Col>
                <Col lg={9}>
                    <div>{loadView(view)}</div>
                </Col>
            </Row>
        </Container>
    );
};

export default AccountDashboardDataLayer;
