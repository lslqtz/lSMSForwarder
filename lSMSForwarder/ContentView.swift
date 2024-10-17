//
//  ContentView.swift
//  lSMSForwarder
//
//  Created by lslqtz on 2024/10/17.
//

import SwiftUI

extension Bundle {
    var AppVer: String {
        return object(forInfoDictionaryKey: "APP_VERSION") as? String ?? "Unknown"
    }
    var MsgServerURL: String {
        return object(forInfoDictionaryKey: "MSG_SERVER_URL") as? String ?? "Unknown"
    }
}

struct ContentView: View {
    var body: some View {
        NavigationView {
            let bundleName = Bundle.main.object(forInfoDictionaryKey: kCFBundleNameKey as String) as! String

            HStack {
                VStack {
                    Text("This app might already work!")
                    Text("Version: \(Bundle.main.AppVer)")
                    Text("Message Server URL: \(Bundle.main.MsgServerURL)").multilineTextAlignment(.center)
                    Spacer()
                }
            }
            .navigationBarTitleDisplayMode(.large)
            .navigationTitle(bundleName)
        }
    }
}

#Preview {
    ContentView()
}
