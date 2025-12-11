export interface BaseLaravelShape {
  config: Record<string, unknown>;
  settings: Record<string, unknown>;
  app: {
    name: string;
    env: string;
    debug: boolean;
    url: string;
  };
  user?: {
    id: number;
    name: string;
    email: string;
  } | null;
}
