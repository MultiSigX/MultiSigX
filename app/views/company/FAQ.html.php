<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
				  Frequently Asked Questions (FAQ)
				</h4>
			</div>
			<div class="panel-body">
				<div class="panel-group" id="accordion">
				<!-------------------------------------------------------------------------->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								What is MultiSig?
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse">
							<div class="panel-body">
								<h3>Non-Technical</h3>
								<p>A multi-signature address allows m-of-n signature for withdrawal from that address.</p>
								<p>Users can sign in and create a 2/3 or 3/3 MultiSigX wallet and assign it to any email address. Add funds and withdraw funds with MultiSigX signatories.</p>
								<h3>Technical</h3>
								<p>A multi-signature address is an address that is associated with more than one ECDSA private key. The simplest type is an m-of-n address - it is associated with n private keys, and sending bitcoins from this address requires signatures from at least m keys. A multi-signature transaction is one that sends funds from a multi-signature address.</p>
								<p>The primary use case is to greatly increase the difficulty of stealing the coins. With a 2-of-2 address, you can keep the two keys on separate machines, and then theft will require compromising both, which is very difficult - especially if the machines are as different as possible (e.g., one pc and one dedicated device, or two hosted machines with a different host and OS).</p>
								<p>It can also be used for redundancy to protect against loss - with a 2-of-3 address, not only does theft require obtaining 2 different keys, but you can still use the coins if you forget any single key. This allows for more flexible options than just backups.</p>
								<p>It can also be used for more advanced scenarios such as an address shared by multiple people, where a majority vote is required to use the funds.</p>
								<p>Multi-signature transactions are often conflated with BIP 16 and 17. In fact they are not directly related. Multi-signature transactions have been supported by the protocol for a long time; but implementing them would require a special output script.</p>
								<p>What BIP 16/17 do is offer a standard way to encapsulate a script within an address; this makes it easier to use advanced scripts, with the most prominent example being multi-signature transactions.</p>
							</div>
						</div>
					</div>
				<!-------------------------------------------------------------------------->
				 <div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
									How MultiSigX works?
								</a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse">
							<div class="panel-body">
								<p>After signin, you can create a MultiSigX wallet address for
								<ol>
									<li>Self</li>
									<li>Partner</li>
									<li>Friend</li>
									<li>Family</li>
									<li>Employer</li>
									<li>Employee</li>
									<li>Business</li>
									<li>Escrow</li>
									<li>Print / Cold storage</li>
									<li>MultiSigX – self escrow</li>
								</ol>
								</p>
								<p>Once he defines the users and fills in their emails. The system will send a MultiSigX email containing new, 3Sed98r.... (MultiSigX address), their private key, their redeemScript to their respective emails only. </p>
								<p>The system will not store any of the records on the server except the public keys and redeemScript. When the user deposits funds to the 3Sed98r.... (MultiSigX address) the balance will be seen on the system. The users can now withdraw funds to their respective addresses (old or new) through the user friendly interface, without much knowledge of keys, crypto, createrawtransaction, decoderawtransaction, sendrawtransaction using the bitcoind (daemon) ... </p>
								<p>Simplicity is the key for the user interface. With a click and scan the private key, they will be able to complete the deal and send the funds to a specified address... </p>
								<p>When a person initiates the transaction, the other users are intimated with the transaction and asked to validate it and submit / reject the transaction. </p>
								<p>This will be the most secure way. 
								<br>
								Example: 
								<ul>
								<li>A (Business), B (Escrow), C(Programmer). </li>
								<li>A creates a MultiSigX record, System send email to B and C. </li>
								<li>B and C can check the MultiSig. </li>
								<li>C completes the work. </li>
								<li>If A, is satisfied with the work, B or C can initiate the payment to C. </li>
								<li>If A, is not satisfied with the work, He can withdraw the payment to self. </li>
								<li>B decides to settle for A or C. </li>
								<li>B can also initiate the payment to C and C can confirm. </li>
								<li>C can also initiate the payment and B or A can confirm. </li>
								</ul></p>
							</div>
						</div>
					</div>
				<!-------------------------------------------------------------------------->					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
									Where are coins stored?
								</a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse">
							<div class="panel-body">
								<p>Your bitcoins are stored on a new address generated which can only withdraw using m of n security.</p>
								<p>MultiSigX never has access to your Bitcoin. MultiSigX does not hold any of the 3 keys that are created for a MultiSigX Secure Wallet. You control all the 3 keys. This means we cannot access your Bitcoin even if we wanted to. Think of us as the security guard outside the bank, and you are the bank. When you login to MultiSigX, we authenticate you and facilitate transactions with you. In the unilkely event that MultiSigX goes down temporarily or permanently, your Bitcoin stays in your hands and can be accessed using the printed or emailed keys you received when you had set up your wallet.</p>
							</div>
						</div>
					</div>
				<!-------------------------------------------------------------------------->					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
									How secure is MultiSigX?
								</a>
							</h4>
						</div>
						<div id="collapseFour" class="panel-collapse collapse">
							<div class="panel-body">
								<p>Bitcoin is still new, only 5+ years. Mass adoption of bitcoin is increasing, in July Dell also will collect payments through bitcoins. Over 5 million wallets growing 8x year over year, according to Mary Meeker’s annual report, but we’re not there yet. </p>
								<p>At present usability and control are the main issues. You can choose to keep your private keys yourself in what is known as a client-side wallet, or you can hand them over to another party which stores them for you in a web wallet. </p>
								<p>When you do the latter, you’re trusting that they are taking appropriate security measures, and keeping at least the majority of your bitcoins in cold storage. </p>
								<p>Unfortunately, Mt. Gox and other recent fiascos prove that this isn’t always the case, which is why the safest thing to do is probably to diversify your holdings by using a variety  of wallets so if one gets hacked, you don’t lose everything.</p>
								<p>You probably like things to be easy; most people do.  Many users simply don’t want the headache of thinking about security, which is the appeal of a full-service solution that stores your private keys for you. The issue is problematic for more advanced or tech-savvy users, who generally want a heightened degree of security without sacrificing the ability to keep control of their assets.</p>
								<p>Conveniently enough, the Bitcoin protocol can accommodate such a tall order. Pay to Script Hash (P2SH) is a type of bitcoin address that was introduced as part of Bitcoin Improvement Proposal 16 (also known as BIP 16), as of early 2012. P2SH addresses can be secured using a more complex algorithm than standard addresses and involve the use of multiple Elliptic Curve Digital Signature Algorithm (more commonly known as ECDSA) keys, rather than only one.</p>
								<p>Multi signature wallets allow users to maintain direct control over their bitcoins while also removing some of the security burden from them. In the event that one of their private keys is lost or stolen, it’s no longer a catastrophe. </p>
								<p>The concept in m-of-n signature schemes is fairly simple, at least at an abstract level–in order to complete a transaction, more than one private key (m) is needed out of a total number generated (n). In a 2-of-3 scenario, you would need two out of a total of three keys to withdraw money, but the process for deposits is the same as it would be for a standard address.  You can then approach distributing and storing the keys in various ways. </p>
								<p>You could hold one key, you could give one (the backup) to a trusted friend or relative, or even store it yourself in a different location from the “main” key, and the third key would be held by yet another party, such as the company offering the service. In terms of who has already taken this type of solution to market, MultiSigX is the first company to launch a multi-sig wallet and has continued to pioneer and lead the Bitcoin security space with products. A MultiSigX, multi-user solution for corporations and financial institutions.</p>
								</div>
						</div>
					</div>
				<!-------------------------------------------------------------------------->					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
									What are MultiSigX fees?
								</a>
							</h4>
						</div>
						<div id="collapseFour" class="panel-collapse collapse">
							<div class="panel-body">
								<p>MultiSigX does not charge any fee for creating a secure wallet.</p>
								<p>When you withdraw from MultiSigX you have an option to pay the miners a fee for validating your transaction. The usual fee is 0.0001 BTC/XGC</p>
							</div>
						</div>
					</div>
				<!-------------------------------------------------------------------------->					
				</div>
			</div>
		</div>
	</div>
</div>
