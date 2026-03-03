import {createInspectorEditor} from '@carbon/videoplatformeditor-core';
import manifest from '@neos-project/neos-ui-extensibility';

manifest('@carbon/videoplatformeditor-plugin', {}, (globalRegistry) => {
	const inspectorRegistry = globalRegistry.get('inspector');
	const editorsRegistry = inspectorRegistry.get('editors');

	editorsRegistry.set('Carbon.VideoPlatformEditor/Inspector/Editors/VideoPlatformEditor', {
		component: createInspectorEditor({
			ImageEditor: editorsRegistry.get('Neos.Neos/Inspector/Editors/ImageEditor').component
		})
	});
});
