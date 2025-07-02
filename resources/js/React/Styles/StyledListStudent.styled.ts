import styled from "styled-components";

const blockColor = "#888";
const blockColorActive = "#FF7F7F";
const textColor = "#222";
const buttonColor = "#444";
const buttonColorActive = "00f";

export const StudentBlock = styled.div`
    display: flex;
    align-items: space-between;
    width: 100%;
    margin: 0px;
    overflow: hidden;
`;

export const Avatar = styled.div`
    display: flex;
    flex: 0 0 50px;
    justify-content: center;
    align-items: center;
    padding: 10px;
`;

export const AvatarImage = styled.img`
    border-radius: 50%;
    width: 50px;
    height: 50px;
`;

export const Details = styled.div`
    padding: 10px;
    color: ${textColor};
    width: 100%;
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    overflow: hidden;
    .name {
        font-size: 1.2rem;
        font-weight: bold;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        h5 {
            .item-author {
                font-size: 1.1rem;
                font-weight: bold;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: ${textColor};
            }
        }

        .item-except {
            font-size: 0.9rem;
            font-weight: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: ${textColor};
        }
    }
    .email {
        font-size: 0.9rem;
        font-weight: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
`;

// Revised CallButton styling
export const CallButton = styled.button`
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: ${(props) =>
        props.active
            ? buttonColorActive
            : "#333"}; // darker color when not active
    border: none;
    cursor: pointer;
    display: flex;
    flex: 1 1 auto;
    margin-right: 10px;
    align-items: center;
    justify-content: center;
    background-color: ${(props) =>
        props.active ? buttonColorActive : buttonColor};

    i.flash {
        animation: flash 0.5s infinite;
    }

    z-index: 100;

    @keyframes flash {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }
`;

export const ButtonBlock = styled.div`
  display: flex;
  flex: 0 0 50px;
  padding-left: 10px;
  width: 100%;
  justify-content: center;
  align-items: center;
  z-index: 100;

  button {
    // Add these styles to center the button within the div
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 110;
  }
`;
