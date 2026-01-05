import styled from "styled-components";

export const StyledCaputureDevices = styled.div`
    width: 100%;
`;

export const StyledCardHeader = styled.div`
    font-weight: 600;
    margin-bottom: 1rem;
`;

export const StyledRow = styled.div`
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
`;

export const StyledCol = styled.div<{ flexBasis?: string }>`
    flex: 1 1 ${(p) => p.flexBasis ?? "auto"};
`;

export const StyledDeviceTitle = styled.div`
    font-weight: 600;
`;

export const StyledButtonGroup = styled.div`
    display: flex;
    gap: 0.5rem;
`;
