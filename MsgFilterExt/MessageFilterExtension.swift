//
//  MessageFilterExtension.swift
//  MsgFilterExt
//
//  Created by lslqtz on 2024/10/17.
//

import IdentityLookup

final class MessageFilterExtension: ILMessageFilterExtension {}

extension MessageFilterExtension: ILMessageFilterQueryHandling {
    func handle(_ queryRequest: ILMessageFilterQueryRequest, context: ILMessageFilterExtensionContext, completion: @escaping (ILMessageFilterQueryResponse) -> Void) {
        let action: ILMessageFilterAction = .none

        context.deferQueryRequestToNetwork() { (networkResponse, error) in
            let response = ILMessageFilterQueryResponse()
            response.action = action
            completion(response)
        }
    }
}
