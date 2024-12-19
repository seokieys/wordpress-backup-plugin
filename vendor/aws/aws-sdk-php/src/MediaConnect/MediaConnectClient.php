<?php
namespace Aws\MediaConnect;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS MediaConnect** service.
 * @method \Aws\Result addBridgeOutputs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addBridgeOutputsAsync(array $args = [])
 * @method \Aws\Result addBridgeSources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addBridgeSourcesAsync(array $args = [])
 * @method \Aws\Result addFlowMediaStreams(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addFlowMediaStreamsAsync(array $args = [])
 * @method \Aws\Result addFlowOutputs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addFlowOutputsAsync(array $args = [])
 * @method \Aws\Result addFlowSources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addFlowSourcesAsync(array $args = [])
 * @method \Aws\Result addFlowVpcInterfaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addFlowVpcInterfacesAsync(array $args = [])
 * @method \Aws\Result createBridge(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBridgeAsync(array $args = [])
 * @method \Aws\Result createFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createFlowAsync(array $args = [])
 * @method \Aws\Result createGateway(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createGatewayAsync(array $args = [])
 * @method \Aws\Result deleteBridge(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBridgeAsync(array $args = [])
 * @method \Aws\Result deleteFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteFlowAsync(array $args = [])
 * @method \Aws\Result deleteGateway(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteGatewayAsync(array $args = [])
 * @method \Aws\Result deregisterGatewayInstance(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deregisterGatewayInstanceAsync(array $args = [])
 * @method \Aws\Result describeBridge(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeBridgeAsync(array $args = [])
 * @method \Aws\Result describeFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeFlowAsync(array $args = [])
 * @method \Aws\Result describeFlowSourceMetadata(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeFlowSourceMetadataAsync(array $args = [])
 * @method \Aws\Result describeFlowSourceThumbnail(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeFlowSourceThumbnailAsync(array $args = [])
 * @method \Aws\Result describeGateway(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeGatewayAsync(array $args = [])
 * @method \Aws\Result describeGatewayInstance(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeGatewayInstanceAsync(array $args = [])
 * @method \Aws\Result describeOffering(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeOfferingAsync(array $args = [])
 * @method \Aws\Result describeReservation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeReservationAsync(array $args = [])
 * @method \Aws\Result grantFlowEntitlements(array $args = [])
 * @method \GuzzleHttp\Promise\Promise grantFlowEntitlementsAsync(array $args = [])
 * @method \Aws\Result listBridges(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBridgesAsync(array $args = [])
 * @method \Aws\Result listEntitlements(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEntitlementsAsync(array $args = [])
 * @method \Aws\Result listFlows(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listFlowsAsync(array $args = [])
 * @method \Aws\Result listGatewayInstances(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listGatewayInstancesAsync(array $args = [])
 * @method \Aws\Result listGateways(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listGatewaysAsync(array $args = [])
 * @method \Aws\Result listOfferings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listOfferingsAsync(array $args = [])
 * @method \Aws\Result listReservations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listReservationsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result purchaseOffering(array $args = [])
 * @method \GuzzleHttp\Promise\Promise purchaseOfferingAsync(array $args = [])
 * @method \Aws\Result removeBridgeOutput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeBridgeOutputAsync(array $args = [])
 * @method \Aws\Result removeBridgeSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeBridgeSourceAsync(array $args = [])
 * @method \Aws\Result removeFlowMediaStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeFlowMediaStreamAsync(array $args = [])
 * @method \Aws\Result removeFlowOutput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeFlowOutputAsync(array $args = [])
 * @method \Aws\Result removeFlowSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeFlowSourceAsync(array $args = [])
 * @method \Aws\Result removeFlowVpcInterface(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeFlowVpcInterfaceAsync(array $args = [])
 * @method \Aws\Result revokeFlowEntitlement(array $args = [])
 * @method \GuzzleHttp\Promise\Promise revokeFlowEntitlementAsync(array $args = [])
 * @method \Aws\Result startFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startFlowAsync(array $args = [])
 * @method \Aws\Result stopFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopFlowAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateBridge(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBridgeAsync(array $args = [])
 * @method \Aws\Result updateBridgeOutput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBridgeOutputAsync(array $args = [])
 * @method \Aws\Result updateBridgeSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBridgeSourceAsync(array $args = [])
 * @method \Aws\Result updateBridgeState(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBridgeStateAsync(array $args = [])
 * @method \Aws\Result updateFlow(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateFlowAsync(array $args = [])
 * @method \Aws\Result updateFlowEntitlement(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateFlowEntitlementAsync(array $args = [])
 * @method \Aws\Result updateFlowMediaStream(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateFlowMediaStreamAsync(array $args = [])
 * @method \Aws\Result updateFlowOutput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateFlowOutputAsync(array $args = [])
 * @method \Aws\Result updateFlowSource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateFlowSourceAsync(array $args = [])
 * @method \Aws\Result updateGatewayInstance(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateGatewayInstanceAsync(array $args = [])
 */
class MediaConnectClient extends AwsClient {}
