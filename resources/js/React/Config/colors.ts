type PaletteColorSet = {
    mainBgColor: string;
    navbarBgColor: string;
    navbarBgColor2: string;
    navbarTextColor: string;
    highlightColor: string;

    sidebarBgColor: string;
    sidebarHeaderBgColor: string;
    sidebarContentBgColor: string;
    sidebarListItemBgColor: string;
    toggleButtonBgColor: string;

    listItemTabBgColor: string;
    listItemTabTextColor: string;
    listItemTabTextActive: string;

    contentBgColor: string;

    active: string;
};

type FrostColorPalette = {
    dark: PaletteColorSet;
    light: PaletteColorSet;
};

const PrimaryColorPalette = {
    100: "#CBCDEE", // Lighter
    200: "#B2B3E5",
    300: "#999ACB",
    400: "#7F85B2",
    500: "#666D99",
    600: "#4C597F",
    700: "#334465",
    800: "#1A2F49",
    900: "#00192F", // Darker 
};

const SecondaryColorPalette = {
    100: "#FFC46B", // Lighter
    200: "#FFC03D",
    300: "#FFB800",
    400: "#D69F00",
    500: "#FF9100",
    600: "#FF8500",
    700: "#FF7800",
    800: "#E56800",
    900: "#FF5C00", // Darker
};

export const colors: FrostColorPalette = {
    dark: {

        /**
         * Main background color
         */
        mainBgColor: PrimaryColorPalette[800],

        /**
         * Navbar background color
         * These to colors are the gradient colors for the titlebar
         */
        navbarBgColor: PrimaryColorPalette[800],
        navbarBgColor2: PrimaryColorPalette[400],

        /**
         * Navbar text color
         * This is used the the Normal state of the text
         */
        navbarTextColor: PrimaryColorPalette[100],

        /**
         * Text Highlight color
         * Used for muse over and active states
         */
        highlightColor: SecondaryColorPalette[500],

        /**
         * Sidebar background color
         * THe Main Sidebar background color
         */
        sidebarBgColor: PrimaryColorPalette[700],

        /**
         * Sidebar header background color
         */
        sidebarHeaderBgColor: PrimaryColorPalette[800],

        /**
         * Sidebar content background color
         */
        sidebarContentBgColor: PrimaryColorPalette[800],

        /**
         * Sidebar list item background color
         */
        sidebarListItemBgColor: PrimaryColorPalette[600],

        /**
         * Sidebar toggle button background color
         */
        toggleButtonBgColor: PrimaryColorPalette[900],

        /**
         * Tab Menu List item background color
         * active: Is the listItemTabBgActive
         */
        listItemTabBgColor: PrimaryColorPalette[700],
        active: PrimaryColorPalette[900],

        /**
         * Tab Menu List item text color
         */
        listItemTabTextColor: PrimaryColorPalette[500],
        listItemTabTextActive: PrimaryColorPalette[100],  

        /**
         * Content background color
         */
        contentBgColor: PrimaryColorPalette[900],
      
      
    },
    light: {
        mainBgColor: PrimaryColorPalette[500],

        /**
         * Navbar background colors
         */
        navbarBgColor: PrimaryColorPalette[200],        
        navbarBgColor2: PrimaryColorPalette[600],

        /**
         * Navbar text color
         * This is used the the Normal state of the text
         */
        navbarTextColor: PrimaryColorPalette[900],
        highlightColor: SecondaryColorPalette[500],

        /**
         * Sidebar background color
         */
        sidebarBgColor: PrimaryColorPalette[200],
        sidebarHeaderBgColor: PrimaryColorPalette[400],
        sidebarContentBgColor: PrimaryColorPalette[300],
        sidebarListItemBgColor: PrimaryColorPalette[300],

        /**
         * Sidebar toggle button background color
         */
        toggleButtonBgColor: PrimaryColorPalette[500],
        active: PrimaryColorPalette[100],

        /**
         * Tab Menu List item background color
         */
        listItemTabBgColor: PrimaryColorPalette[500],
        listItemTabTextColor: PrimaryColorPalette[500],
        listItemTabTextActive: PrimaryColorPalette[800],

        /**
         * Content background color
         */
        contentBgColor: PrimaryColorPalette[100],
    },
};
