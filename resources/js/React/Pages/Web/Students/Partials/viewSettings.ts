import { ReactNode } from "react";
import { ComponentType } from "react"; // Add this import

import OfflineClassRoom from "../Dashboard/Rooms/Offline/OfflineClassRoom";
import WaitingRoom from "../Dashboard/Rooms/Waiting/WaitingRoom";
import DNCStudent from "../Dashboard/Rooms/Banned/DNCStudent";
import VirtualClassroom from "../Dashboard/Rooms/VirtualClassRoom";
import PendingVerification from "../Dashboard/Rooms/Verification/PendingVerification";
import Maintenance from "../../../Maintenance";

export interface ViewSettings {
  [key: string]: ComponentType<any>; // Update the type to ComponentType<any>
}

export const views: ViewSettings = {
  "class-not-in-session": OfflineClassRoom,
  "pending-meeting-start": WaitingRoom,
  "validate-dnc": DNCStudent,
  "virtual-class": VirtualClassroom,
  "pending-verification": PendingVerification,
  "down-for-maintenance": Maintenance,
};
