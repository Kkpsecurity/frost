import { createContext, useState } from "react";

interface SpotlightTourContextProps {
    isHelpModalOpen: boolean;
    setIsHelpModalOpen: React.Dispatch<React.SetStateAction<boolean>>;
}

const SpotlightTourContext = createContext<SpotlightTourContextProps>({
    isHelpModalOpen: false,
    setIsHelpModalOpen: () => {},
});

const { Provider } = SpotlightTourContext;

type SpotlightTourProviderProps = {
    children: React.ReactNode;
};

const SpotlightTourProvider: React.FC<SpotlightTourProviderProps> = ({ children }) => {
    const [isHelpModalOpen, setIsHelpModalOpen] = useState(false);

    return (
        <Provider value={{ isHelpModalOpen, setIsHelpModalOpen }}>
            {children}
        </Provider>
    );
};

export { SpotlightTourContext, SpotlightTourProvider };
