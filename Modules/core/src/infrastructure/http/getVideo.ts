import {fetchWithErrorHandling} from '@neos-project/neos-ui-backend-connector';
import {IVideo} from "../../domain";

type GetVideoQuery = {
    videoUri: string;
};

type GetVideoQueryResultEnvelope =
    | {
          success: IVideo;
      }
    | {
          error: {
              message: string;
          };
      };

export async function getVideo(
    query: GetVideoQuery
): Promise<GetVideoQueryResultEnvelope> {
    try {
        const response = await fetchWithErrorHandling.withCsrfToken(
            (csrfToken) => ({
                url:
                    '/neos/carbon/video-platform/video',
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-Flow-Csrftoken': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    videoUri: query.videoUri
                })
            })
        );

        return fetchWithErrorHandling.parseJson(response);
    } catch (error) {
        fetchWithErrorHandling.generalErrorHandler(error as any);
        throw error;
    }
}
