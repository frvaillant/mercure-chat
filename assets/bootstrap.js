import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    './controllers',
    true,
    /\.[jt]sx?$/
));
