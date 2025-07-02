import React from "react";
import styled, { keyframes } from "styled-components";

const LoaderWrapper = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
`;
const base_url = window.location.origin;
const Loader = () => {
    return (
        <LoaderWrapper>
            <div>
                <img src={base_url + "/assets/img/loading.gif"} />
            </div>
        </LoaderWrapper>
    );
};

export default Loader;
