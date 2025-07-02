import { createContext } from 'react';
import { AgoraConfigType, ZoomConfigType, SiteConfigType } from '../Config/types';

interface FrostConfigContextType {
  zoom: ZoomConfigType;
  agora: AgoraConfigType;
  site: SiteConfigType;
}

const defaultConfig: FrostConfigContextType = {
  zoom: {
    api_key: "",
    api_secret: "",
    meeting_sdk: "",
    meeting_secret: "",
    api_url: "",
    token_life: 0,
    authentication_method: "",
    max_api_calls_per_request: 0,
    signature_endpoint: "",
    screen_share_url: "",
  },
  agora: {
    app_id: "",
    certificate: "",
    rtc: {
      endpoint: "",
      expire_minutes: 0,
    },
  },
  site: {
    base_url: "",
  },
};

export const FrostConfigContext = createContext<FrostConfigContextType>(defaultConfig);
