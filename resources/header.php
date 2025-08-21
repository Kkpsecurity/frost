.header-one {
    position: absolute;
    width: 100%;
    height: calc(var(--topbar-height) + var(--botbar-height));
    top: 0;
    left: 0;
    margin: auto;
    background-color: var(--frost-secondary-color);
    box-shadow: 0 0 15px rgba(19, 11, 11, 0.3);
    z-index: 9999;
}
.topbar-area {
    background: var(--frost-secondary-color);
}

.topbar-left {
    width: 100%;
    height: var(--frost-topbar-height);
    margin: 0;
    position: relative;
    background-color: var(--frost-base-color);
}

.topbar-left::before {
    left: -110px;
    width: 115px;
    z-index: -1;
}

.topbar-left::after {
    content: "";
    position: absolute;
    right: -50px;
    top: 0;
    border-top: var(--frost-topbar-height) solid transparent;
    border-bottom: 0 solid transparent;
    border-left: 50px solid var(--frost-base-color);
    z-index: 10;
}

.topbar-left .date-greeter {
    display: flex;
    align-items: center;
    justify-content: start;
    vertical-align: middle;
    height: var(--frost-topbar-height);
    font-size: 14px;
    font-weight: 700;
    color: var(--frost-primary-color);
    text-transform: uppercase;
}

.topbar-left i {
    font-size: 16px;
    margin-right: 10px;
    font-weight: 700;
    color: var(--frost-primary-color);
}

@media screen and (max-width: 768px) {
    .topbar-left {
        display: none;
    }
}

.top_right_nav {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    height: var(--frost-topbar-height);
    padding-right: 10px;
}

.login-button,
.register-button {
    background-color: var(--frost-login-btn-color);
    color: var(--frost-white-color);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.5;
    text-transform: uppercase;
    border-radius: 5px;
    padding: 5px 10px;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}

.login-button:hover,
.register-button:hover {
    background-color: var(--frost-login-btn-hover-color) !important;
    transition: background-color 0.3s ease;
}

@media screen and (max-width: 768px) {
    .button-text {
        display: none;
    }
    .login-button,
    .register-button {
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 12px;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

.user-profile {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.5;
    text-transform: uppercase;
    color: var(--frost-white-color);
    margin-right: 10px;
}

.acircle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--frost-info-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--frost-white-color);
    font-size: 1rem;
    margin-right: 5px;
}

.acircle:last-child {
    margin-right: 10px;
    background-color: var(--frost-danger-color);
}

.header-area {
    position: relative;
    height: var(--frost-botbar-height);
    background-color: var(--frost-primary-color);
    z-index: 999;
}

.desktop-view {
    margin-top: -2px;
}

.logo {
    height: var(--botbar-height);
    display: flex;
    margin-top: -5px;
    align-items: center;
}

.logo a {
    display: inline-block;
    padding: 0 15px;
    font-size: 1.7rem;
    font-weight: 700;
    line-height: var(--frost-botbar-height);
    text-transform: uppercase;
}

.navbar-brand {
    font-size: 1.7rem;
    font-weight: bold;
    color: var(--frost-light-color);
}
.navbar-brand:hover {
    color: var(--frost-warning-color);
}
.mobile-view {
    display: none;
}
@media screen and (max-width: 768px) {
    .desktop-view {
        display: none;
    }
    .mobile-view {
        display: block;
        font-size: 24px;
    }
}

/** Navigate **/


/* Toggler */
.navbar-toggler {
    font-size: 1.25rem;
    line-height: 1;
    background-color: transparent;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 15px;
    display: none;
}
.navbar-toggler:focus {
    outline: none;
}
.navbar-toggler span {
    color: var(--frost-light-color);
}

/* Main menu */
.navbar-nav {
    display: flex;
    align-items: center;
    margin-right: 0;
    height: var(--frost-botbar-height);
}

.navbar-nav .nav-item {
    margin-right: 20px;
}

.navbar-nav .nav-link {
    display: flex;
    align-items: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 16px;
    font-weight: 600;
    width: auto;
    height: calc(var(--frost-botbar-height) - 20px);
    color: var(--frost-white-color);
    text-transform: uppercase;
    padding: 0 15px;
    margin: 0;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover {
    background-color: transparent;
    color: var(--frost-warning-color);
}

.navbar-nav .nav-link.active {
    background-color: transparent;
    color: var(--frost-warning-color);
}

.navbar-nav .nav-link.active:hover {
    background-color: transparent;
    color: var(--frost-warning-color);
}

.main-menu ul.navbar-nav li {
    float: left;
    position: relative;
}

.main-menu ul.navbar-nav li a {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    color: var(--frost-light-color);
    font-size: 16px;
    font-weight: 700;
    padding: 25px 15px;
    text-transform: uppercase;
    position: relative;
}

.main-menu ul.navbar-nav li a:hover {
    color: var(--frost-info-color);
}

.main-menu ul.navbar-nav li.active a:focus {
    color: var(--frost-info-color);
}

.main-menu ul.navbar-nav li.active a {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    color: var(--frost-info-color);
    position: relative;
    z-index: 9999999;
}
/*--------------------------------*/
/* 2.2. Sticky Header Area
/*--------------------------------*/
.header-area.stick {
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 999999;
    box-shadow: 0px 0px 3px #151b2c, -2px -2px 3px #151b2c;
    background: #1b2654;
}
.header-area.stick .logo a {
    display: inline-block;
    height: auto;
    padding: 17px 0;
}
.header-area.stick .main-menu ul.nav li ul.sub-menu li a {
    color: #fff;
    display: block;
    padding: 5px 15px;
}
@media (min-width: 320px) and (max-width: 767.98px) {
    .navbar-toggler {
        position: absolute;
        right: 25px;
        top: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        border: none;
        outline: none;
        z-index: 999;
        padding: 0;
    }

    .navbar-toggler span {
        display: block;
        width: 25px;
        height: 2px;
        margin: 5px 0;
        transition: all 0.3s ease;
    }

    .navbar-toggler span:first-child {
        transform-origin: top left;
    }

    .navbar-toggler span:last-child {
        transform-origin: bottom left;
    }

    .navbar-toggler.active span:first-child {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .navbar-toggler.active span:last-child {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    .navbar-toggler.active span:nth-child(2) {
        opacity: 0;
    }

    .navbar-collapse {
        position: fixed;
        top: calc(var(--frost-botbar-height) + 50px);
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 99999;
        background-color: var(--frost-secondary-color);
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .navbar-collapse.show {
        transform: translateX(0);
    }

    .navbar-nav {
        flex-direction: column;
        justify-content: start;
        align-items: start;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .navbar-nav .nav-link:hover {
        color: var(--frost-warning-color);
    }

    .main-menu ul.navbar-nav li {
        float: none;
        position: relative;
        margin: 0;
        transition: all 0.3s ease;
    }

    .main-menu ul.navbar-nav li a {
        background: transparent;
        color: var(--frost-white-color);
        font-size: 1.5rem;
        font-weight: 700;
        padding: 20px;
        text-transform: uppercase;
        position: relative;
        transition: all 0.3s ease;
    }

    .main-menu ul.navbar-nav li a:hover {
        color: var(--frost-warning-color);
    }

    .main-menu ul.navbar-nav li.active a:focus {
        color: var(--frost-warning-color);
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .navbar-toggler {
        position: absolute;
        right: 25px;
        top: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        border: none;
        outline: none;
        z-index: 999;
        padding: 0;
    }

    .navbar-toggler span {
        display: block;
        width: 25px;
        height: 2px;
        margin: 5px 0;
        transition: all 0.3s ease;
    }

    .navbar-toggler span:first-child {
        transform-origin: top left;
    }

    .navbar-toggler span:last-child {
        transform-origin: bottom left;
    }

    .navbar-toggler.active span:first-child {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .navbar-toggler.active span:last-child {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    .navbar-toggler.active span:nth-child(2) {
        opacity: 0;
    }

    .navbar-collapse {
        position: fixed;
        top: calc(var(--frost-botbar-height) + 50px);
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 99999;
        background-color: var(--frost-secondary-color);
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .navbar-collapse.show {
        transform: translateX(0);
    }

    .navbar-nav {
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 40px 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .navbar-nav .nav-item {
        margin: 20px 0;
    }

    .navbar-nav .nav-link {
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 2.5rem;
        color: var(--frost-white-color);
        text-transform: uppercase;
        padding: 0;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: var(--frost-warning-color);
    }

    .main-menu ul.navbar-nav li {
        float: none;
        position: relative;
        margin: 0;
    }

    .main-menu ul.navbar-nav li a {
        background: transparent;
        color: var(--frost-white-color);
        font-size: 1.5rem;
        font-weight: 700;
        padding: 20px;
        text-transform: uppercase;
        position: relative;
    }

    .main-menu ul.navbar-nav li a:hover {
        color: var(--frost-warning-color);
    }

    .main-menu ul.navbar-nav li.active a:focus {
        color: var(--frost-warning-color);
    }
}
