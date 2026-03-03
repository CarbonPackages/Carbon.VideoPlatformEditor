import {fetchWithErrorHandling} from '@neos-project/neos-ui-backend-connector';

type GetVideoQuery = {
    videoId: string;
};

type GetVideoQueryResultEnvelope =
    | {
          success: {
              posterImageId: string;
              videoTitle: string;
          };
      }
    | {
          error: {
              type: string;
              code: number;
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
                    id: query.videoId
                })
            })
        );

        return fetchWithErrorHandling.parseJson(response);
    } catch (error) {
        fetchWithErrorHandling.generalErrorHandler(error as any);
        throw error;
    }
}
